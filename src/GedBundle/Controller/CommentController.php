<?php

namespace GedBundle\Controller;

use GedBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CommentController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    public function addAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $fileRepository = $em->getRepository('GedBundle:File');
        $userRepository = $em->getRepository('GedBundle:User');

        $inputComment =  filter_var($request->get('comment')) ;
        $userId = $request->get('user');

        if( ! $file = $fileRepository->find($id) )
        {
            throw new ResourceNotFoundException( sprintf('There is no file with %d id', $id));
        }

        if( $userId == null )
        {
            throw new AccessDeniedException("You need to be connected to add a comment");
        } else if( !$user = $userRepository->find($userId) )
        {
            throw new ResourceNotFoundException( sprintf('There is no user with %d id', $id));
        }

        $comment = new Comment();
        $comment->setCreatedBy($user);
        $comment->setFile($file);
        $comment->setName('Comment');
        $comment->setContent($inputComment);
        $comment->setCreated(new \DateTime());

        $date = $comment->getCreated();
        $date = date_format($date, "H:i  F m");
        $status = true;

        $em->persist($comment);
        $em->flush();

        $response = new JsonResponse();
        return $response->setData(array( 'date' => $date, 'status' => $status, 'comment' => $inputComment ));

    }

    public function removeAction(Request $request, $id)
    {

    }
}
