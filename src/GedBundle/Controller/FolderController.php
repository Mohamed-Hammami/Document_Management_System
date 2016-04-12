<?php

namespace GedBundle\Controller;


use Sonata\AdminBundle\Controller\CRUDController;
use GedBundle\Utils\FieldDescription;
use GedBundle\Entity\File;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use GedBundle\Utils\BatchActionDescription;

class FolderController extends CRUDController
{

    private $folderActionsValues = array("Delete");
    private $fileActionsValues = array("Delete");

    private $folderHeader = array();
    private $folderMappedFields = array();
    private $folderActions = array();

    private $fileHeader = array();
    private $fileMappedFields = array();
    private $fileActions = array();


    /**
     * @return array
     */
    public function getFolderHeader()
    {
        return $this->folderHeader;
    }

    public function folderListAction($id=1)
    {

    // I have to test on the existence of folder id $id

        $fileRepository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('GedBundle:File');

        $folderRepository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('GedBundle:Folder');

        $files = $fileRepository->findFileUser($id);

        $children = $folderRepository->findFolderChildrenUser($id);

        dump($children);
        dump($files);

        $this->addFolderHeader('Name', 'text');
        $this->addFolderHeader('Created at', 'datetime');
        $this->addFolderHeader('Updated at', 'datetime');
        $this->addFolderHeader('Created by', 'string');
        $this->addFolderHeader('Last updated by', 'string');

        $this->addFolderMappedField('name');
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
        $this->addFileAction(new BatchActionDescription('delete', false));

        return $this->render($this->admin->getTemplate('folderList'), array(

            'folder_id'      => $id,

            'children'       => $children,
            'files'          => $files,

            'folder_headers' => $this->folderHeader,
            'folder_fields'  => $this->folderMappedFields,

            'file_headers'   => $this->fileHeader,
            'file_fields'    => $this->fileMappedFields,

            'folder_actions'   => $this->folderActions,
            'file_actons'      => $this->fileActions,
        ));
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

    /**
     * @return array
     */
    public function getFolderMappedFields()
    {
        return $this->folderMappedFields;
    }

    /**
     * @param array $folderMappedFields
     */
    public function setFolderMappedFields($folderMappedFields)
    {
        $this->folderMappedFields = $folderMappedFields;
    }

    /**
     * @return array
     */
    public function getFolderActions()
    {
        return $this->folderActions;
    }

    /**
     * @param array $folderActions
     */
    public function setFolderActions($folderActions)
    {
        $this->folderActions = $folderActions;
    }

    /**
     * @return array
     */
    public function getFileHeader()
    {
        return $this->fileHeader;
    }

    /**
     * @param array $fileHeader
     */
    public function setFileHeader($fileHeader)
    {
        $this->fileHeader = $fileHeader;
    }

    /**
     * @return array
     */
    public function getFileMappedFields()
    {
        return $this->fileMappedFields;
    }

    /**
     * @param array $fileMappedFields
     */
    public function setFileMappedFields($fileMappedFields)
    {
        $this->fileMappedFields = $fileMappedFields;
    }

    /**
     * @return array
     */
    public function getFileActions()
    {
        return $this->fileActions;
    }

    /**
     * @param array $fileActions
     */
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


        dump($action);
        dump($this->folderActionsValues);

        if( !in_array($this->camelize($action), $this->folderActionsValues) )
        {
            throw $this->createNotFoundException('This batch does not exist');
        }

        // at least one item must be checked
        $nonRelevent = (count($idx) < 1 && !$allElements);

        dump($nonRelevent);

        if( $nonRelevent )
        {
            $nonRelevantMessage = 'flash_batch_empty';
            $this->addFlash('sonata_flash_info', $nonRelevantMessage);

            return new RedirectResponse(
                  $this->generateUrl('admin_ged_folder_folderList', array('id' => $id))
            );
        }

        $finalAction = sprintf('batchFolder%s', $action);

        if (!is_callable(array($this, $finalAction))) {
            throw new \RuntimeException(sprintf('A `%s::%s` method must be callable', get_class($this), $finalAction));
        }

        dump($data);

        return call_user_func(array($this, $finalAction), $data, $id, $request);

    }

    public function batchFolderDelete(array $data, $id, Request $request = null)
    {
        // Control security with $this->admin->checkAccess do it below !!!

        // I have also to add a model manager exception

        $folderRepository = $this->getDoctrine()->getRepository('GedBundle:Folder');
        $em = $this->getDoctrine()->getManager();


        try{

            if( !$data['all_elements'] )
            {
                foreach( $data['idx'] as $value )
                {
                    if( !($folder = $folderRepository->find($value)) )
                    {
                        $this->createNotFoundException(sprintf("There's no folder with %d id"), $value);
                    } else
                    {
                        $em->remove($folder);
                    }
                }

                $em->flush();
            } else
            {
                $children = $folderRepository->getChildren($id, true);
                foreach( $children as $child)
                {
                    $em->remove($child);
                }

                $em->flush();
            }

            $this->addFlash('sonata_flash_success', 'flash_batch_delete_success');

        } catch( ModelManagerException $ex )
        {
            $this->addFlash('sonata_flash_error', 'flash_batch_delete_error');
        }

        return new RedirectResponse(
            $this->generateUrl('admin_ged_folder_folderList', array('id' => $id))
        );

    }

    /**
     * @param string $property
     *
     * @return string
     */
    public function camelize($property)
    {
        return Container::camelize($property);
    }
}
