<?php

namespace GedBundle\Controller;


use GedBundle\Entity\Folder;
use GedBundle\Utils\FieldDescription;
use GedBundle\Entity\File;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use GedBundle\Utils\BatchActionDescription;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use GedBundle\Form\Type\FolderType;

class FolderController extends Controller
{

    private $flashBagTypes = array('success', 'warning', 'info', 'danger');

    private $folderActionsValues = array("Delete", "Modify", "Move");
    private $fileActionsValues = array("Delete", "Modify", "Move");

    private $folderHeader = array();
    private $folderMappedFields = array();
    private $folderActions = array();

    private $fileHeader = array();
    private $fileMappedFields = array();
    private $fileActions = array();


    public function getFolderHeader()
    {
        return $this->folderHeader;
    }

    public function showAction(Request $request, $id)
    {

    // I have to test on the existence of folder id $id

        $em = $this->getDoctrine()->getManager();

        $fileRepository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('GedBundle:File');

        $folderRepository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('GedBundle:Folder');

        if( $id == 0)
        {
            $folder = new Folder();
            $folder->setName('Root');
            $folder->setDescription('The root folder');
            $folder->setCreated(new \DateTime('now'));

            $em->persist($folder);
            $em->flush();
        }

        $currentFolder = $folderRepository->find($id);

        $path = $folderRepository->getPath($currentFolder);

        if( !$currentFolder )
        {
            throw new ResourceNotFoundException;
        }

        $files = $fileRepository->findFileUser($id);
        $children = $folderRepository->findFolderChildrenUser($id);
        $path = $folderRepository->getPath($currentFolder);

        $this->addFolderHeader('Name', 'text');
        $this->addFolderHeader('Description', 'text');
        $this->addFolderHeader('Created at', 'datetime');
        $this->addFolderHeader('Updated at', 'datetime');
        $this->addFolderHeader('Created by', 'string');
        $this->addFolderHeader('Last updated by', 'string');

        $this->addFolderMappedField('name');
        $this->addFolderMappedField('description');
        $this->addFolderMappedField('created');
        $this->addFolderMappedField('updated');
        $this->addFolderMappedField('createdBy');
        $this->addFolderMappedField('updatedBy');

        $this->addFileHeader('Name', 'text');
        $this->addFileHeader('Description', 'text');
        $this->addFileHeader('Created at', 'datetime');
        $this->addFileHeader('Updated at', 'datetime');
        $this->addFileHeader('Created by', 'string');
        $this->addFileHeader('Last updated by', 'string');

        $this->addFileMappedField('name');
        $this->addFileMappedField('description');
        $this->addFileMappedField('created');
        $this->addFileMappedField('updated');
        $this->addFileMappedField('createdBy');
        $this->addFileMappedField('updatedBy');

        $this->addFolderAction( new BatchActionDescription('delete', false));
        $this->addFolderAction( new BatchActionDescription('move', false));

        $this->addFileAction(new BatchActionDescription('delete', false));
        $this->addFileAction(new BatchActionDescription('move', false));

        return $this->render('@Ged/CRUD/folder.html.twig', array(

            'folder_id'      => $id,
            'path'           => $path,

            'children'       => $children,
            'files'          => $files,

            'folder_headers' => $this->folderHeader,
            'folder_fields'  => $this->folderMappedFields,

            'file_headers'   => $this->fileHeader,
            'file_fields'    => $this->fileMappedFields,

            'folder_actions'   => $this->folderActions,
            'file_actions'      => $this->fileActions,

        ));


    }


    public function createAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $folderRepository =  $em->getRepository('GedBundle:Folder');


        $folder = new Folder();
        if ( !$rootFolder = $folderRepository->find($id) )
            throw new NotFoundHttpException( sprintf('There is no folder with %d id', $id));

        $folder->setParent($rootFolder);

        $form = $this->createForm(FolderType::class, $folder);
        $form->add('create', 'submit', array(
            'label' => 'create',
            'attr' => array(
                'class' => 'btn btn-primary'
            )));


        $form->handleRequest($request);

        if( $form->isValid() )
        {

            if ( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') )
            {
                throw $this->createAccessDeniedException('You have to be authenticated to create a folder');
            }
            $user = $this->get('security.token_storage')->getToken()->getUser();

            $folder->setCreatedBy($user);

            $em->persist($folder);

            $em->flush();

            return $this->redirect($this->generateUrl('folder_show', array('id' => ($folder->getId()))));

        }

        return $this->render(
            '@Ged/CRUD/folderCreate.html.twig',
            array(
                'form' => $form->createView(),
                'id' => $id,
            )
        );
    }

    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $folderRepository =  $em->getRepository('GedBundle:Folder');


        if ( !$folder = $folderRepository->find($id) )
            throw new NotFoundHttpException( sprintf('There is no folder with %d id', $id));


        $form = $this->createForm(FolderType::class, $folder);
        $form->add('edit', 'submit', array(
            'label' => 'edit',
            'attr' => array(
                'class' => 'btn btn-primary'
            )))
            ->add('cancel', 'reset', array(
                'label' => 'cancel',
                'attr' => array(
                    'class' => 'btn btn-primary'
            )));


        $form->handleRequest($request);

        if( $form->isValid() )
        {

            if ( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') )
            {
                throw $this->createAccessDeniedException('You have to be authenticated to edit a folder');
            }

            $em->flush();

            return $this->redirect($this->generateUrl('folder_show', array('id' => ($folder->getId()))));

        }

        return $this->render(
            '@Ged/CRUD/folderEdit.html.twig',
            array(
                'form' => $form->createView(),
                'id' => $id,
            )
        );
    }

    public function addFolderHeader($label, $type)
    {
        $folderDesc = new FieldDescription($label, $type);
        $this->folderHeader[] = $folderDesc;
    }

    public function addFolderMappedField($field)
    {
        $this->folderMappedFields[] = $field;
    }

    public function addFileHeader($label, $type)
    {
        $fileDesc = new FieldDescription($label, $type);
        $this->fileHeader[] = $fileDesc;
    }

    public function addFileMappedField($field)
    {
        $this->fileMappedFields[] = $field;
    }

    public function addFolderAction(BatchActionDescription $action)
    {
        $this->folderActions[] = $action;
    }

    public function addFileAction(BatchActionDescription $action)
    {
        $this->fileActions[] = $action;
    }

    public function getFolderMappedFields()
    {
        return $this->folderMappedFields;
    }

    public function setFolderMappedFields($folderMappedFields)
    {
        $this->folderMappedFields = $folderMappedFields;
    }

    public function getFolderActions()
    {
        return $this->folderActions;
    }

    public function setFolderActions($folderActions)
    {
        $this->folderActions = $folderActions;
    }

    public function getFileHeader()
    {
        return $this->fileHeader;
    }

    public function setFileHeader($fileHeader)
    {
        $this->fileHeader = $fileHeader;
    }

    public function getFileMappedFields()
    {
        return $this->fileMappedFields;
    }

    public function setFileMappedFields($fileMappedFields)
    {
        $this->fileMappedFields = $fileMappedFields;
    }

    public function getFileActions()
    {
        return $this->fileActions;
    }

    public function setFileActions($fileActions)
    {
        $this->fileActions = $fileActions;
    }

    public function folderBatchAction(Request $request = null)
    {
        // Checks for the form method

        if (Request::getHttpMethodParameterOverride() || !$request->request->has('_method')) {
            $restMethod = $request->getMethod();
        } else {
            $restMethod = $request->request->get('_method');
        }

        if ('POST' !== $restMethod) {
            throw $this->createNotFoundException(sprintf('Invalid request type "%s", POST expected', $restMethod));
        }

        if ($data = json_decode($request->get('data'), true)) {
            $id          = $data['id'];
            $action      = $data['action'];
            $idx         = $data['idx'];
            $allElements = $data['all_elements'];
            $request->request->replace(array_merge($request->request->all(), $data));
        } else {
            $request->request->set('idx', $request->get('idx', array()));
            $request->request->set('all_elements', $request->get('all_elements', false));

            $id          = $request->get('currentFolder');
            $action      = $request->get('action');
            $idx         = $request->get('idx');
            $allElements = $request->get('all_elements');
            $data        = $request->request->all();
        }


        if( !in_array($this->camelize($action), $this->folderActionsValues) )
        {
            throw $this->createNotFoundException('This batch does not exist');
        }

        // at least one item must be checked
        $nonRelevent = (count($idx) < 1 && !$allElements);

        if( $nonRelevent )
        {
            $this->addFlash('warning', 'Action aborted. No items were selected.');

            return new RedirectResponse(
                  $this->generateUrl('folder_show', array('id' => $id))
            );
        }

        $finalAction = sprintf('batchFolder%s', $action);

        if (!is_callable(array($this, $finalAction))) {
            throw new \RuntimeException(sprintf('A `%s::%s` method must be callable', get_class($this), $finalAction));
        }

        return call_user_func(array($this, $finalAction), $data, $id, $request);

    }

    public function batchFolderDelete(array $data, $id, Request $request = null)
    {
        // Control security with $this->admin->checkAccess do it below !!!

        // I also have to add a model manager exception

        $folderRepository = $this->getDoctrine()->getRepository('GedBundle:Folder');

            if( !$data['all_elements'] )
            {
                foreach( $data['idx'] as $value )
                {
                    $this->removeFolder($value);
                }

            } else
            {
                $folder = $folderRepository->find($id);
                $children = $folderRepository->getChildren($folder, true);
                foreach( $children as $child)
                {
                    $this->removeFolder($child->getId());
                }
            }

            $this->addFlash('success', 'Selected folders have been successfully deleted.');


        return new RedirectResponse(
            $this->generateUrl('folder_show', array('id' => $id))
        );

    }

    public function batchFolderMove(array $data, $id, Request $request = null)
    {
        // I didnt implement security check yet !!!

        $em = $this->getDoctrine()->getEntityManager();
        $folderRepository = $em->getRepository('GedBundle:Folder');

        if( $folders = $this->get('session')->get('cutFolders') )
        {
            foreach( $folders as $folder )
            {
                $folder = $folderRepository->find($folder->getId());
                $folder->setOnHold(false);
            }
            $folders = array();

        } else
        {
            $folders = array();
        }

        if( !$data['all_elements'] )
        {
            foreach( $data['idx'] as $value )
            {
                $folder = $folderRepository->find($value);
                array_push($folders, $folder);
                $folder->setOnHold(true);
            }

        } else
        {
            $folder = $folderRepository->find($id);
            $children = $folderRepository->getChildren($folder, true);
            foreach( $children as $child)
            {
                array_push($folders, $child);
                $child->setOnHold(true);
            }
        }

        $this->get('session')->set('cutFolders', $folders);
        $em->flush();

        return new RedirectResponse(
            $this->generateUrl('folder_show', array('id' => $id)));

    }
    public function fileBatchAction(Request $request = null)
    {
        // Checks for the form method

        if (Request::getHttpMethodParameterOverride() || !$request->request->has('_method')) {
            $restMethod = $request->getMethod();
        } else {
            $restMethod = $request->request->get('_method');
        }

        if ('POST' !== $restMethod) {
            throw $this->createNotFoundException(sprintf('Invalid request type "%s", POST expected', $restMethod));
        }

        if ($data = json_decode($request->get('data'), true)) {
            $id          = $data['id'];
            $action      = $data['action'];
            $idx         = $data['idx'];
            $allElements = $data['all_elements'];
            $request->request->replace(array_merge($request->request->all(), $data));
        } else {
            $request->request->set('idx', $request->get('idx', array()));
            $request->request->set('all_elements', $request->get('all_elements', false));

            $id          = $request->get('currentFolder');
            $action      = $request->get('action');
            $idx         = $request->get('idx');
            $allElements = $request->get('all_elements');
            $data        = $request->request->all();
        }


        if( !in_array($this->camelize($action), $this->fileActionsValues) )
        {
            throw $this->createNotFoundException('This batch does not exist');
        }

        // at least one item must be checked
        $nonRelevent = (count($idx) < 1 && !$allElements);

        if( $nonRelevent )
        {
            $this->addFlash('warning', 'Action aborted. No items were selected.');

            return new RedirectResponse(
                $this->generateUrl('folder_show', array('id' => $id))
            );
        }

        $finalAction = sprintf('batchFile%s', $action);

        if (!is_callable(array($this, $finalAction))) {
            throw new \RuntimeException(sprintf('A `%s::%s` method must be callable', get_class($this), $finalAction));
        }


        return call_user_func(array($this, $finalAction), $data, $id, $request);

    }

    public function batchFileDelete(array $data, $id, Request $request = null)
    {
        // Control security with $this->admin->checkAccess do it below !!!

        $em = $this->getDoctrine()->getManager();
        $fileRepository = $em->getRepository('GedBundle:File');

        if( !$data['all_elements'] )
        {
            foreach( $data['idx'] as $fileId )
            {
                $this->removeFile($fileId, $id);
            }

        } else
        {
            $filesId = $fileRepository->findFileIdByFolderId($id);
            foreach( $filesId as $fileId)
            {
                $this->removeFile($fileId, $id);
            }
        }

        $this->addFlash('success', 'Selected files have been successfully deleted.');

        return new RedirectResponse(
            $this->generateUrl('folder_show', array('id' => $id))
        );

    }

    public function batchFileMove(array $data, $id, Request $request = null)
    {

        // I didnt implement security check yet !!!
        $em = $this->getDoctrine()->getEntityManager();
        $fileRepository = $this->getDoctrine()->getRepository('GedBundle:File');


        if( $files = $this->get('session')->get('cutFiles') )
        {
            foreach( $files as $file )
            {
                $file = $fileRepository->find($file->getId());
                $file->setOnHold(false);
            }
            $files = array();

        } else {
            $files = array();
        }

        if( !$data['all_elements'] )
        {
            foreach( $data['idx'] as $value )
            {
                $file = $fileRepository->find($value);
                array_push($files, $file);
                $file->setOnHold(true);
            }

        } else
        {
            $files = $fileRepository->findFileByFolder($id);
            foreach( $files as $file)
            {
                $file->setOnHold(true);
            }
        }

        $this->get('session')->set('cutFiles', $files);
        $em->flush();

        return new RedirectResponse(
            $this->generateUrl('folder_show', array('id' => $id)));

    }

    public function camelize($property)
    {
        return Container::camelize($property);
    }

    public function addFlash($type, $message)
    {
//        if( ! in_array($type, $this->flashBagTypes) )
//        {
//            throw $this->createNotFoundException('This flashBag type does not exist');
//        }

        $this->get('session')
            ->getFlashBag()
            ->add($type, $message)
        ;
    }

    public function removeFile($fileId, $folderId)
    {

        $em = $this->getDoctrine()->getManager();

        $fileRepository = $em->getRepository('GedBundle:File');
        $versionRepository = $em->getRepository('GedBundle:Version');
        $commentRepository = $em->getRepository('GedBundle:Comment');
        $tagsRepository = $em->getRepository('GedBundle:Tag');
        $folderRepository = $em->getRepository('GedBundle:Folder');


        if( !$file = $fileRepository->find($fileId) )
        {
            throw new ResourceNotFoundException( sprintf('There is no file with %d id', $fileId));
        }

        if( !$folder = $folderRepository->find($folderId) )
        {
            throw new ResourceNotFoundException( sprintf('There is no folder with %d id', $folderId));
        }

        if( !$file->isOnHold() )
        {
            throw new AccessDeniedException(sprintf("You can't delete an on holde file"));
        }

        $folder->removeFile($file);

        $versions = $versionRepository->findVersionsByFile($fileId);
        $comments = $commentRepository->findAllCommentsByFile($fileId);
        $tags = $tagsRepository->findTagsByFile($fileId);

        foreach( $tags as $tag)
        {
            if( $tagsRepository->countNodes($tag->getId()) <= 1 )
                $em->remove($tag);
        }

        foreach( $versions as $version)
        {
            $em->remove($version);
        }

        foreach( $comments as $comment)
        {
            $em->remove($comment);
        }

        $em->remove($file);
        $em->flush();

    }

    public function removeFolder($folderId)
    {
        $em = $this->getDoctrine()->getManager();
        $fileRepository = $em->getRepository('GedBundle:File');
        $folderRepository = $em->getRepository('GedBundle:Folder');

        if( !($folder = $folderRepository->find($folderId)) )
        {
            throw $this->createNotFoundException(sprintf("There's no folder with %d id"), $folderId);
        }

        if( $folder->isOnHold() )
        {
            throw $this->createAccessDeniedException(sprintf("You can't delete an on holder folder"));
        }


        $filesId = $fileRepository->findFileIdByFolderId($folderId);


        foreach( $filesId as $fileId )
            $this->removeFile($fileId['id'], $folderId);

        $em->remove($folder);
        $em->flush();
    }

    public function pasteFoldersAction(Request $request, $parentId)
    {
        // security check needed

        $em = $this->getDoctrine()->getEntityManager();
        $folderRepository = $em->getRepository('GedBundle:Folder');

        $childIds = $request->get('childIds');
        $sourceId = $request->get('sourceId');



        if( !($parentFolder = $folderRepository->find($parentId)) )
        {
            throw $this->createNotFoundException(sprintf("There's no folder with %d id"), $parentId);
        }

        if( !($sourceFolder = $folderRepository->find($sourceId)) )
        {
            throw $this->createNotFoundException(sprintf("There's no folder with %d id"), $parentId);
        }


        foreach( $childIds as $childId )
        {
            if( !($childFolder = $folderRepository->find($childId)) )
            {
                throw $this->createNotFoundException(sprintf("There's no folder with %d id"), $childId);
            }

            if( $childId == $parentId )
            {
                throw $this->createAccessDeniedException(sprintf("You can't paste the folders here"));
            }

            $childrenFolders = $folderRepository->children($childFolder);

            // Performance problem here

            foreach( $childrenFolders as $child )
            {
                if( $child->getId() == $parentId )
                {
                    throw $this->createAccessDeniedException(sprintf("You can't paste the folders here"));
                }
            }

            $this->pasteFolder($parentFolder, $sourceFolder, $childFolder);
        }

        $em->flush();
        $this->get('session')->remove('cutFolders');
        $response = new JsonResponse();
        return $response->setData( array('status' => 'success'));
    }

    private function pasteFolder($parent, $source, $child)
    {

        // Optimisation problem if I test for names

        if( $parent->getId() == $source->getId() )
            $child->setOnHold(false);
        else {

            $child->setParent($parent);
            $child->setOnHold(false);
        }

        return true;

    }

    public function resetFoldersAction(Request $request)
    {
        // security check needed

        $foldersIds = $request->get('cutFolders');

        dump($foldersIds);

        $em = $this->getDoctrine()->getEntityManager();
        $folderRepository = $em->getRepository('GedBundle:Folder');

        foreach( $foldersIds as $folderId )
        {
            if( !($folder = $folderRepository->find($folderId)) )
            {
                throw $this->createNotFoundException(sprintf("There's no folder with %d id"), $folderId);
            }

            $folder->setOnHold(false);
        }

        $em->flush();
        $this->get('session')->remove('cutFolders');
        $response = new JsonResponse();
        return $response->setData( array('status' => 'success'));

    }

    public function pasteFilesAction(Request $request, $parentId)
    {
        // security check needed

        $em = $this->getDoctrine()->getEntityManager();
        $folderRepository = $em->getRepository('GedBundle:Folder');
        $fileRepository = $em->getRepository('GedBundle:File');

        $fileIds = $request->get('fileIds');
        $sourceId = $request->get('sourceId');


        if( !($parentFolder = $folderRepository->find($parentId)) )
        {
            throw $this->createNotFoundException(sprintf("There's no folder with %d id"), $parentId);
        }

        if( !($sourceFolder = $folderRepository->find($sourceId)) )
        {
            throw $this->createNotFoundException(sprintf("There's no folder with %d id"), $parentId);
        }

        foreach( $fileIds as $fileId )
        {
            if( !($file = $fileRepository->find($fileId)) )
            {
                throw $this->createNotFoundException(sprintf("There's no file with %d id"), $fileId);
            }

            $this->pasteFile($parentFolder, $sourceFolder, $file);
        }

        $em->flush();
        $this->get('session')->remove('cutFiles');
        $response = new JsonResponse();
        return $response->setData( array('status' => 'success'));


    }

    public function pasteFile($folder, $source, $file)
    {

        if( $folder->getId() == $source->getId() )
            $file->setOnHold(false);
        else
        {

            $source->removeFile($file);
            $file->setFolder($folder);
            $file->setOnHold(false);
        }
    }

    public function resetFilesAction(Request $request)
    {
        // security check needed

        $filesIds = $request->get('cutFiles');

        $em = $this->getDoctrine()->getEntityManager();
        $fileRepository = $em->getRepository('GedBundle:File');

        foreach( $filesIds as $fileId )
        {
            if( !($file = $fileRepository->find($fileId)) )
            {
                throw $this->createNotFoundException(sprintf("There's no file with %d id"), $fileId);
            }

            $file->setOnHold(false);
        }

        $em->flush();
        $this->get('session')->remove('cutFiles');
        $response = new JsonResponse();
        return $response->setData( array('status' => 'success'));

    }
}
