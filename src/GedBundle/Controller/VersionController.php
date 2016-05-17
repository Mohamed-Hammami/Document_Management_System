<?php

namespace GedBundle\Controller;

use GedBundle\Entity\Version;
use GedBundle\Form\Type\VersionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class VersionController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    public function createAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $fileRepository = $em->getRepository('GedBundle:File');

        $version = new Version();

        if( !$file = $fileRepository->find($id) )
        {
            throw new ResourceNotFoundException( sprintf('There is no file with %d id', $id));
        }

        $form = $this->createForm(VersionType::class, $version);
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
                throw $this->createAccessDeniedException('You have to be authenticated to create a file version');
            }
            $user = $this->get('security.token_storage')->getToken()->getUser();

            $version->setFileName($file->getName().'_0');
            $version->setName($file->getName().md5(uniqid()));

            $size = $version->getFileContent()->getSize();
            $version->setSize($size);

            $version->setCreatedBy($user);
            $file->addVersion($version);
            $file->setUpdatedBy($user);

            $em->persist($version);
            $em->persist($file);

            $em->flush();

            return $this->redirect($this->generateUrl('file_show', array('id' => ($file->getId()))));
        }

        return $this->render(
            'GedBundle:CRUD:versionCreate.html.twig',
            array(
                'form' => $form->createView(),
                'id' => $id,
            )
        );

    }
}
