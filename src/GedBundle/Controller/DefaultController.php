<?php

namespace GedBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {

        if ( !$user = $this->get('security.token_storage')->getToken()->getUser())
        {
            throw $this->createAccessDeniedException(sprintf('You need to be authenticated'));
        }

        $em = $this->getDoctrine()->getEntityManager();
        $workspaceRepository = $em->getRepository('GedBundle:Workspace');

        $workspace = $workspaceRepository->findOneBy(array('user' => $user));
        $memo =  $workspace->getMemo();

        $this->get('session')->set('memo', $memo);

        return $this->render('GedBundle:Default:index.html.twig');
    }
}
