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

        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository('GedBundle:GroupeFile');

        $query = $rep->findGroupFileByFile(1);

        dump($query);

        return $this->render('GedBundle:Default:index.html.twig');
    }
}
