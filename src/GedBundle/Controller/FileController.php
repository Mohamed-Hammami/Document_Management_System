<?php

namespace GedBundle\Controller;

use GedBundle\Entity\File;
use GedBundle\Entity\Version;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
                'form' => $form->createView(),
                'id' => $id,
            )
        );

    }

    public function showAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $fileRepository = $em->getRepository('GedBundle:File');
        $versionRepository = $em->getRepository('GedBundle:Version');
        $folderRepository = $em->getRepository('GedBundle:Folder');
        $tagsRepository = $em->getRepository('GedBundle:Tag');

        if( !$file = $fileRepository->find($id) )
        {
            throw new ResourceNotFoundException( sprintf('There is no file with %d id', $id));
        }

        $fileComment = $fileRepository->findFileComment($id);
        $versions = $versionRepository->findVersionsByFile($id);
        $folder = $folderRepository->findFolderByFile($id);
        $versionCreators = $versionRepository->findCreators($id);
        $fileCreators = $fileRepository->findCreators($id);

        $tagsName = $this->extractTags($fileRepository->findTagsName($id));

        $path = $this->buildPath($folder, $file);
        $creators = $versionCreators + $fileCreators;

        dump($fileComment);
        dump($path);
        dump($versions);
        dump($creators);
        dump($tagsName);


        return $this->render('@Ged/CRUD/fileShow.html.twig', array(

            'path'     => $path,
            'file'     => $fileComment,
            'versions' => $versions,
            'creators' => $creators,
            'tags'     => $tagsName,
        ));
    }


    public function addNewVersion(Version $version, File $file, $user)
    {

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

//    public function downloadVersionAction(Version $version)
//    {
//        $downloadHandler = $this->get('vich_uploader.download_handler');
//
//        return $downloadHandler->downloadObject($version, $fileField = 'imageFile');
//    }
}
