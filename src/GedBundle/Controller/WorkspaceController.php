<?php

namespace GedBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class WorkspaceController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    public function showAction(Request $request)
    {

    }

    public function addFileAction(Request $request, $id)
    {
        if ( !$user = $this->get('security.token_storage')->getToken()->getUser())
        {
            throw $this->createAccessDeniedException(sprintf('You need to be authenticated to add a file to your workspace'));
        }

        $em = $this->getDoctrine()->getEntityManager();
        $workspaceRepository = $em->getRepository('GedBundle:Workspace');
        $fileRepository = $em->getRepository('GedBundle:File');
        $workspace = $workspaceRepository->findOneBy(array('user' => $user));

        if( $file = $fileRepository->find($id) )
        {
            throw $this->createNotFoundException(sprintf("there's no file with %id id", $id));
        }

        

    }

    public function removeFileAction(Request $request, $id)
    {

    }

    public function addFolderAction(Request $request, $id)
    {

    }

    public function removeFolderAction(Request $request, $id)
    {

    }

    public function rateFileAction(Request $request, $id)
    {

    }

    public function rateFolderAction(Request $request, $id)
    {

    }

    public function notifyFileAction(Request $request, $id)
    {

    }

    public function notifyFolderAction(Request $request, $id)
    {

    }
}
