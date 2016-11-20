<?php

namespace GedBundle\Controller;

use GedBundle\Entity\File;
use GedBundle\Entity\Version;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use GedBundle\Form\Type\FileType;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;


class FileController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    public function createAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $folderRepository =  $em->getRepository('GedBundle:Folder');

        $file = new File();
        if ( $folderRepository->find($id) )
            $folder = $folderRepository->find($id);
        else
            throw new ResourceNotFoundException( sprintf('There is no folder with %d id', $id));


        $file->setFolder($folder);

        $form = $this->createForm(FileType::class, $file);
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
                throw $this->createAccessDeniedException('You have to be authenticated to create a file');
            }
            $user = $this->get('security.token_storage')->getToken()->getUser();

            $file->setCreatedBy($user);

            if ( $version = $form->get('version')->getData() )
            {
                $version = $this->addNewVersion($version, $file, $user);
                $version->setFile($file);
                $em->persist($version);
            }
            $em->persist($file);

            $em->flush();

            return $this->redirect($this->generateUrl('file_show', array('id' => ($file->getId()))));
        }

        return $this->render(
            '@Ged/CRUD/fileCreate.html.twig',
            array(
                'form' => $form,
                'id' => $id,
            )
        );

    }

    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $fileRepository =  $em->getRepository('GedBundle:File');

        if( !$file = $fileRepository->find($id) )
        {
            throw $this->createNotFoundException(sprintf("there's no file with %id id", $id));
        }

        $form = $this->createForm(FileType::class, $file, array('edit' => true));
        $form->add('edit', 'submit', array(
            'label' => 'edit',
            'attr' => array(
                'class' => 'btn btn-primary'
            )))
            ->add('cancel', 'reset', array(
                'label' => 'cancel',
                'attr' => array(
                    'class' => 'btn btn-primary'
                )))
        ;

        $form->handleRequest($request);
        if( $form->isValid() )
        {
            if ( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') )
            {
                throw $this->createAccessDeniedException('You have to be authenticated to edit a file');
            }

            $em->flush();

            return $this->redirect($this->generateUrl('file_show', array('id' => ($file->getId()))));
        }

        return $this->render(
            '@Ged/CRUD/fileEdit.html.twig',
            array(
                'form' => $form->createView(),
                'id' => $id,
            )
        );

    }

    public function showAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $fileRepository = $em->getRepository('GedBundle:File');
        $groupeRepository = $em->getRepository('GedBundle:Groupe');
        $versionRepository = $em->getRepository('GedBundle:Version');
        $folderRepository = $em->getRepository('GedBundle:Folder');
        $commentRepository = $em->getRepository('GedBundle:Comment');
        $groupFileRepository = $em->getRepository('GedBundle:GroupeFile');

        if( !$file = $fileRepository->find($id) )
        {
            throw new ResourceNotFoundException( sprintf('There is no file with %d id', $id));
        }

        if ( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') )
        {
            throw $this->createAccessDeniedException('You have to be authenticated to view a file');
        }

        $this->denyAccessUnlessGranted('view', $file);

        $user = $this->get('security.token_storage')->getToken()->getUser();
        //$file = $fileRepository->find($id);
        $comments = $commentRepository->findCommentsByFile($id, 5);
        $versions = $versionRepository->findVersionsByFile($id);
        $folder = $folderRepository->findFolderByFile($id);
        $versionCreators = $versionRepository->findCreators($id);
        $fileCreators = $fileRepository->findCreators($id);
        $groupFile = $groupFileRepository->findGroupFileByFile($id);

        $tagsName = $this->extractTags($fileRepository->findTagsName($id));

        $ids = $groupFileRepository->findGroupIds($id);
        $groups = $groupeRepository->findFreeGroups($id);


        $path = $this->buildPath($folder, $file);
        $creators = $versionCreators + $fileCreators;

        dump($user);

        return $this->render('@Ged/CRUD/fileShow.html.twig', array(

            'file_id'  => $id,
            'path'     => $path,
            'file'     => $file,
            'comments' => $comments,
            'versions' => $versions,
            'creators' => $creators,
            'tags'     => $tagsName,
            'user'     => $user,
            'permissions' => $groupFile,
        ));
    }


    public function addNewVersion(Version $version, File $file, $user)
    {


        $this->denyAccessUnlessGranted('edit', $file);

        $version->setFileName($file->getName().'_0');
        $version->setName($file->getName().md5(uniqid()));

        $size = $version->getFileContent()->getSize();
        $version->setSize($size);

       $version->setCreatedBy($user);

        return $version;
    }

    public function getFormattedSize($size)
    {

        $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }

    public function buildPath($folder, $file)
    {

        $folderRepository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('GedBundle:Folder');


        $path = $folderRepository->getPath($folder);

        $path[] = $file;

        return $path;
    }

    public function extractTags($input)
    {
        if( count($input) == 0 || $input[0]['tg_name'] == null )
        {
            return null;
        } else
        {
            $tags = array();
            foreach( $input as $element )
            {
                $tags[] = $element['tg_name'];
            }

            return $tags;
        }
    }

    public function downloadVersionAction(Version $version)
    {

        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        $path = $helper->asset($version, '$fileContent');
    }

    public function lockAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $fileRepository = $em->getRepository('GedBundle:File');

        if( !$file = $fileRepository->find($id) )
        {
            throw new ResourceNotFoundException( sprintf('There is no file with %d id', $id));
        }

        $this->denyAccessUnlessGranted('control', $file);
        $file->setLocked(true);

        $em->flush();

        $response = new JsonResponse();
        return $response->setData(array('status' => 'success'));

    }

    public function unlockAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $fileRepository = $em->getRepository('GedBundle:File');

        if( !$file = $fileRepository->find($id) )
        {
            throw new ResourceNotFoundException( sprintf('There is no file with %d id', $id));
        }

        $this->denyAccessUnlessGranted('control', $file);
        $file->setLocked(false);

        $em->flush();

        $response = new JsonResponse();
        return $response->setData(array('status' => 'success'));

    }

}
