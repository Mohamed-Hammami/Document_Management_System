<?php

namespace GedBundle\Controller;

use GedBundle\Entity\Folder;
use GedBundle\Entity\OnHold;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {

        if ( !$user = $this->get('security.token_storage')->getToken()->getUser())
        {
            throw $this->createAccessDeniedException(sprintf('You need to be authenticated'));
        }

        $em = $this->getDoctrine()->getEntityManager();
//        $configRepository = $em->getRepository('GedBundle:Config');
//
//        $config = $configRepository->find(0);
//
//        if( $config->isFirstTime() )
//        {
//
//            $root = new Folder();
//            $root->setName("Root Folder");
//            $root->setDescription('The root folder');
//            $config->setRootId($root->getId());
//
//            $em->persist($root);
//
//            $config->setSkin("skin-blue");
//            $config->setLanguage("en");
//            $config->setFirstTime(false);
//
//            $em->flush();
//        }

        $workspaceRepository = $em->getRepository('GedBundle:Workspace');
        $fileRepository = $em->getRepository('GedBundle:File');
        $folderRepository = $em->getRepository('GedBundle:Folder');

        $folders = $folderRepository->findOnHold();
        $files = $fileRepository->findOnHold();

        foreach( $folders as  $folder )
        {
            $folder->setOnHold(false);
        }

        foreach( $files as $file )
        {
            $file->setOnHold(false);
        }

        $em->flush();

        $workspace = $workspaceRepository->findOneBy(array('user' => $user));
        $memo =  $workspace->getMemo();

        $this->get('session')->set('memo', $memo);

        return $this->render('GedBundle:Default:index.html.twig');

//        return $this->redirectToRoute('folder_show', array('id' => $config->getRootId()));
    }
}
