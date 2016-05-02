<?php

namespace GedBundle\Controller;

use GedBundle\Entity\File;
use GedBundle\Entity\Version;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use GedBundle\Form\Type\FileType;
use Vich\UploaderBundle\Form\Type\VichFileType;

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
            throw new NotFoundHttpException( sprintf('There is no folder with %d id', $id));

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
                $em->persist($version);
            }
            $em->persist($file);

            $em->flush();

            return $this->redirect($this->generateUrl('file_show'), array('id' => ($file->getId())));
        }

        return $this->render(
            '@Ged/CRUD/fileCreate.html.twig',
            array('form' => $form->createView())
        );

    }

    public function showAction(Request $request, $id)
    {

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
}
