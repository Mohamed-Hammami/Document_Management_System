<?php

namespace GedBundle\Controller;

use GedBundle\Entity\Workspace;
use GedBundle\Entity\WorkspaceFile;
use GedBundle\Entity\WorkspaceFolder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class WorkspaceController extends Controller
{

    private $folderHeader = array();
    private $fileHeader = array();

    private $folderMappedFields = array();
    private $fileMappedFields = array();

    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    public function showAction(Request $request)
    {
        if ( !$user = $this->get('security.token_storage')->getToken()->getUser())
        {
            throw $this->createAccessDeniedException(sprintf('You need to be authenticated to add a file to your workspace'));
        }


        $em = $this->getDoctrine()->getEntityManager();
        $workspaceRepository = $em->getRepository('GedBundle:Workspace');
        $workspaceFileRepository = $em->getRepository('GedBundle:WorkspaceFile');
        $workspaceFolderRepository = $em->getRepository('GedBundle:WorkspaceFolder');

        $workspace = $user->getWorkspace();
        $files = $workspaceFileRepository->findFilesByWorkspace($workspace->getId());
        $folders = $workspaceFolderRepository->findFoldersByWorkspace($workspace->getId());



        dump($files);
        dump($folders);

        return $this->render(
            'GedBundle:CRUD:workspace.html.twig',
            array(

                'user' => $user,
                'files' => $files,
                'folders' => $folders,

            ));


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
        $workspaceFileRepository = $em->getRepository('GedBundle:WorkspaceFile');
        $workspace = $workspaceRepository->findOneBy(array('user' => $user));

        if( !$file = $fileRepository->find($id) )
        {
            throw $this->createNotFoundException(sprintf("there's no file with %d id", $id));
        }

        if( $workspaceFile = $workspaceFileRepository->findOneBy(array('file' => $file, 'workspace' => $workspace)))
        {
            throw $this->createNotFoundException(sprintf("there's already a  workspaceFile with %d file's id and %d workspace's  id", $id, $workspace->getId()));
        }

        $workspaceFile = new WorkspaceFile($workspace, $file);
        $em->persist($workspaceFile);
        $em->flush();

        $response = new JsonResponse();
        return $response->setData(array('status' => 'success'));

    }

    public function removeFileAction(Request $request, $id)
    {

        // Missing security check

        if ( !$user = $this->get('security.token_storage')->getToken()->getUser())
        {
            throw $this->createAccessDeniedException(sprintf('You need to be authenticated to add a file to your workspace'));
        }

        $em = $this->getDoctrine()->getEntityManager();
        $workspaceRepository = $em->getRepository('GedBundle:Workspace');
        $fileRepository = $em->getRepository('GedBundle:File');
        $workspaceFileRepository = $em->getRepository('GedBundle:WorkspaceFile');

        $workspace = $workspaceRepository->findOneBy(array('user' => $user));

        if( !$file = $fileRepository->find($id) )
        {
            throw $this->createNotFoundException(sprintf("there's no file with %d id", $id));
        }

        if( !$workspaceFile = $workspaceFileRepository->findOneBy(array('file' => $file, 'workspace' => $workspace)))
        {
            throw $this->createNotFoundException(sprintf("there's no workspaceFle with %d file's id and %d workspace's  id", $id, $workspace->getId()));
        }

        $em->remove($workspaceFile);
        $em->flush();

        $response = new JsonResponse();
        return $response->setData(array('status' => 'success'));

    }

    public function addFolderAction(Request $request, $id)
    {
        if ( !$user = $this->get('security.token_storage')->getToken()->getUser())
        {
            throw $this->createAccessDeniedException(sprintf('You need to be authenticated to add a folder to your workspace'));
        }

        $em = $this->getDoctrine()->getEntityManager();
        $workspaceRepository = $em->getRepository('GedBundle:Workspace');
        $folderRepository = $em->getRepository('GedBundle:folder');
        $workspaceFolderRepository = $em->getRepository('GedBundle:WorkspaceFolder');
        $workspace = $workspaceRepository->findOneBy(array('user' => $user));

        if( !$folder = $folderRepository->find($id) )
        {
            throw $this->createNotFoundException(sprintf("there's no folder with %d id", $id));
        }

        if( $workspaceFolder = $workspaceFolderRepository->findOneBy(array('folder' => $folder, 'workspace' => $workspace)))
        {
            throw $this->createNotFoundException(sprintf("there's already a  workspaceFolder with %d folder's id and %d workspace's  id", $id, $workspace->getId()));
        }

        $workspaceFolder = new WorkspaceFolder($workspace, $folder);
        $em->persist($workspaceFolder);
        $em->flush();

        $response = new JsonResponse();
        return $response->setData(array('status' => 'success'));

    }

    public function removeFolderAction(Request $request, $id)
    {
        if ( !$user = $this->get('security.token_storage')->getToken()->getUser())
        {
            throw $this->createAccessDeniedException(sprintf('You need to be authenticated to add a folder to your workspace'));
        }

        $em = $this->getDoctrine()->getEntityManager();
        $workspaceRepository = $em->getRepository('GedBundle:Workspace');
        $folderRepository = $em->getRepository('GedBundle:Folder');
        $workspaceFolderRepository = $em->getRepository('GedBundle:WorkspaceFolder');

        $workspace = $workspaceRepository->findOneBy(array('user' => $user));

        if( !$folder = $folderRepository->find($id) )
        {
            throw $this->createNotFoundException(sprintf("there's no folder with %d id", $id));
        }

        if( !$workspaceFolder = $workspaceFolderRepository->findOneBy(array('folder' => $folder, 'workspace' => $workspace)))
        {
            throw $this->createNotFoundException(sprintf("there's no workspaceFle with %d folder's id and %d workspace's  id", $id, $workspace->getId()));
        }

        $em->remove($workspaceFolder);
        $em->flush();

        $response = new JsonResponse();
        return $response->setData(array('status' => 'success'));

    }

    public function rateFileAction(Request $request, $id)
    {
        if ( !$user = $this->get('security.token_storage')->getToken()->getUser())
        {
            throw $this->createAccessDeniedException(sprintf('You need to be authenticated to rate a file'));
        }

        $em = $this->getDoctrine()->getEntityManager();
        $workspaceRepository = $em->getRepository('GedBundle:Workspace');
        $workspaceFileRepository = $em->getRepository('GedBundle:WorkspaceFile');
        $fileRepository = $em->getRepository('GedBundle:File');
        $workspace = $user->getWorkspace();

        if( !$file = $fileRepository->find($id) )
        {
            throw $this->createNotFoundException(sprintf("there's no file with %d id", $id));
        }

        if( !$workspaceFile = $workspaceFileRepository->findOneBy(array('file' => $file, 'workspace' => $workspace)))
        {
            throw $this->createNotFoundException(sprintf("there's no workspaceFle with %d file's id and %d workspace's  id", $id, $workspace->getId()));
        }

        $workspaceFile->setRating($request->get('rating'));
        $em->flush();

        $response = new JsonResponse();
        return $response->setData(array('status' => 'success'));

    }

    public function rateFolderAction(Request $request, $id)
    {
        if ( !$user = $this->get('security.token_storage')->getToken()->getUser())
        {
            throw $this->createAccessDeniedException(sprintf('You need to be authenticated to rate a folder'));
        }

        $em = $this->getDoctrine()->getEntityManager();
        $workspaceFolderRepository = $em->getRepository('GedBundle:WorkspaceFolder');
        $folderRepository = $em->getRepository('GedBundle:folder');
        $workspace = $user->getWorkspace();

        if( !$folder = $folderRepository->find($id) )
        {
            throw $this->createNotFoundException(sprintf("there's no folder with %d id", $id));
        }


        if( !$workspaceFolder = $workspaceFolderRepository->findOneBy(array('folder' => $folder, 'workspace' => $workspace)))
        {
            throw $this->createNotFoundException(sprintf("there's no workspaceFolder with %d folder's id and %d workspace's  id", $id, $workspace->getId()));
        }

        $workspaceFolder->setRating($request->get('rating'));
        $em->flush();

        $response = new JsonResponse();
        return $response->setData(array('status' => 'success'));

    }

    public function notifyFileAction(Request $request, $id)
    {
        if ( !$user = $this->get('security.token_storage')->getToken()->getUser())
        {
            throw $this->createAccessDeniedException(sprintf('You need to be authenticated to subscribe to a file'));
        }

        $em = $this->getDoctrine()->getEntityManager();
        $workspaceRepository = $em->getRepository('GedBundle:Workspace');
        $workspaceFileRepository = $em->getRepository('GedBundle:WorkspaceFile');
        $fileRepository = $em->getRepository('GedBundle:File');
        $workspace = $user->getWorkspace();

        if( !$file = $fileRepository->find($id) )
        {
            throw $this->createNotFoundException(sprintf("there's no file with %d id", $id));
        }

        if( !$workspaceFile = $workspaceFileRepository->findOneBy(array('file' => $file, 'workspace' => $workspace)))
        {
            throw $this->createNotFoundException(sprintf("there's no workspaceFle with %d file's id and %d workspace's  id", $id, $workspace->getId()));
        }

        $workspaceFile->setNotification(1);
        $em->flush();

        $response = new JsonResponse();
        return $response->setData(array('status' => 'success'));
    }

    public function unnotifyFileAction(Request $request, $id)
    {
        if ( !$user = $this->get('security.token_storage')->getToken()->getUser())
        {
            throw $this->createAccessDeniedException(sprintf('You need to be authenticated to subscribe to a file'));
        }

        $em = $this->getDoctrine()->getEntityManager();
        $workspaceRepository = $em->getRepository('GedBundle:Workspace');
        $workspaceFileRepository = $em->getRepository('GedBundle:WorkspaceFile');
        $fileRepository = $em->getRepository('GedBundle:File');
        $workspace = $user->getWorkspace();

        if( !$file = $fileRepository->find($id) )
        {
            throw $this->createNotFoundException(sprintf("there's no file with %d id", $id));
        }

        if( !$workspaceFile = $workspaceFileRepository->findOneBy(array('file' => $file, 'workspace' => $workspace)))
        {
            throw $this->createNotFoundException(sprintf("there's no workspaceFle with %d file's id and %d workspace's  id", $id, $workspace->getId()));
        }

        $workspaceFile->setNotification(0);
        $em->flush();

        $response = new JsonResponse();
        return $response->setData(array('status' => 'success'));
    }

    public function notifyFolderAction(Request $request, $id)
    {
        if ( !$user = $this->get('security.token_storage')->getToken()->getUser())
        {
            throw $this->createAccessDeniedException(sprintf('You need to be authenticated subscribe to a folder'));
        }

        $em = $this->getDoctrine()->getEntityManager();
        $workspaceFolderRepository = $em->getRepository('GedBundle:WorkspaceFolder');
        $folderRepository = $em->getRepository('GedBundle:Folder');
        $workspace = $user->getWorkspace();

        if( !$folder = $folderRepository->find($id) )
        {
            throw $this->createNotFoundException(sprintf("there's no folder with %d id", $id));
        }

        if( !$workspaceFolder = $workspaceFolderRepository->findOneBy(array('folder' => $folder, 'workspace' => $workspace)))
        {
            throw $this->createNotFoundException(sprintf("there's no workspaceFle with %d folder's id and %d workspace's  id", $id, $workspace->getId()));
        }

        $workspaceFolder->setNotification(1);
        $em->flush();

        $response = new JsonResponse();
        return $response->setData(array('status' => 'success'));
    }

    public function unnotifyFolderAction(Request $request, $id)
    {
        if ( !$user = $this->get('security.token_storage')->getToken()->getUser())
        {
            throw $this->createAccessDeniedException(sprintf('You need to be authenticated subscribe to a folder'));
        }

        $em = $this->getDoctrine()->getEntityManager();
        $workspaceFolderRepository = $em->getRepository('GedBundle:WorkspaceFolder');
        $folderRepository = $em->getRepository('GedBundle:Folder');
        $workspace = $user->getWorkspace();

        if( !$folder = $folderRepository->find($id) )
        {
            throw $this->createNotFoundException(sprintf("there's no folder with %d id", $id));
        }

        if( !$workspaceFolder = $workspaceFolderRepository->findOneBy(array('folder' => $folder, 'workspace' => $workspace)))
        {
            throw $this->createNotFoundException(sprintf("there's no workspaceFle with %d folder's id and %d workspace's  id", $id, $workspace->getId()));
        }

        $workspaceFolder->setNotification(0);
        $em->flush();

        $response = new JsonResponse();
        return $response->setData(array('status' => 'success'));
    }

    public function setWorkspaceAction(Request $request, $userId)
    {

        // Temporary function for test use only

        $em = $this->getDoctrine()->getEntityManager();
        $workspaceRepository = $em->getRepository('GedBundle:Workspace');
        $userRepository = $em->getRepository('GedBundle:User');

        if( !$user = $userRepository->find($userId) )
        {
            throw $this->createNotFoundException(sprintf("There's no user with %id id", $userId));
        }

        $workspace = new Workspace();
        $workspace->setUser($user);

        $em->persist($workspace);
        $em->flush();

        return $this->redirect($this->generateUrl('ged_bundle_home'));
    }

    public function saveMemoAction(Request $request)
    {
        if ( !$user = $this->get('security.token_storage')->getToken()->getUser())
        {
            throw $this->createAccessDeniedException(sprintf('You need to be authenticated to rate a folder'));
        }

        $em = $this->getDoctrine()->getEntityManager();
        $workspaceRepository = $em->getRepository('GedBundle:Workspace');

        $workspace = $workspaceRepository->findOneBy(array('user' => $user));
        $memo = $request->get('memo');
        $workspace->setMemo($memo);

        $this->get('session')->set('memo', $memo);

        $em->flush();

        $response = new JsonResponse();
        return $response->setData(array('status' => 'success'));
    }

    public function resetMemoAction(Request $request)
    {
        if ( !$user = $this->get('security.token_storage')->getToken()->getUser())
        {
            throw $this->createAccessDeniedException(sprintf('You need to be authenticated to rate a folder'));
        }

        $em = $this->getDoctrine()->getEntityManager();
        $workspaceRepository = $em->getRepository('GedBundle:Workspace');

        $workspace = $workspaceRepository->findOneBy(array('user' => $user));
        $workspace->setMemo(null);

        $this->get('session')->set('memo', null);

        $em->flush();

        $response = new JsonResponse();
        return $response->setData(array('status' => 'success'));
    }

}
