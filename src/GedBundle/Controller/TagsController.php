<?php

namespace GedBundle\Controller;

use GedBundle\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class TagsController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    public function addAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $fileRepository = $em->getRepository('GedBundle:File');
        $tagRepository = $em->getRepository('GedBundle:Tag');

        if( !$file = $fileRepository->find($id) )
        {
            throw new ResourceNotFoundException( sprintf('There is no file with %d id', $id));
        }

        $tags = $tagRepository->findAll();
        $inputTag = $this->inputControl($request->get('tag'));
        $fileTags = explode(' ', $request->get('file_tags'));

        $exist = false;
        $response =  new JsonResponse();

        if( in_array($inputTag, $fileTags) )
        {
            return  $response->setData(array('exist' => true) );

        } else
        {
            foreach( $tags as $tag )
            {
                if( $tag->getName() == $inputTag )
                {
                    $file->addTag($tag);
                    $em->flush();
                    $exist = true;
                    break;
                }
            }

            if( !$exist )
            {
                $tag = new Tag();
                $tag->setName($inputTag);
                $file->addTag($tag);
                $em->persist($tag);
                $em->flush();
            }

            return $response->setData(array( 'tag' => $tag->getName(), 'exist' => false ));
        }

    }

    public function dropAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $fileRepository = $em->getRepository('GedBundle:File');
        $tagRepository = $em->getRepository('GedBundle:Tag');

        $result = $tagRepository->countNodes(15);

        dump($result);

        return 0;

    }

    public function inputControl($input)
    {
        $result = explode(' ', $input);
        $result = strtolower($result[0]);
        $result = ucfirst($result);

        return $result;
    }
}
