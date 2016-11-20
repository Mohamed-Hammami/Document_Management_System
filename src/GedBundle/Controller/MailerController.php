<?php

namespace GedBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use GedBundle\Entity\User;

class MailerController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    public function composeAction(Request $request)
    {

        if( !$user = $this->get('security.token_storage')->getToken()->getUser() )
        {
            throw $this->createAccessDeniedException(sprintf("You need to be authentified to compose a mail"));
        }


        if( !$destination = $request->get('destination') )
            $destination = null;

        return $this->render(
            'GedBundle:Mail:compose.html.twig',
            array( 'destination' => $destination)
        );

    }

    public function composeToAction(Request $request, $to)
    {
        $response = $this->forward('GedBundle:Mailer:compose', array(
           'destination' => $to
        ));

        return $response;
    }

    public function sendAction(Request $request)
    {
        if( !$user = $this->get('security.token_storage')->getToken()->getUser() )
        {
            throw $this->createAccessDeniedException(sprintf("You need to be authentified to send a mail"));
        }

        $text = $request->get('text');
        $destination = $request->get('destination');
        $subject = $request->get('subject');

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setTo($destination)
            ->setFrom("dmsservermail@gmail.com")
            ->setBody(
                $this->renderView(
                    'GedBundle:Mail:template.html.twig',
                    array(
                        'user' => $user,
                        'text' => $text,
                    )
                )
            )
            ->setContentType('text/html');

        if( $this->get('mailer')->send($message) )
        {
            $this->get('session')
                ->getFlashBag()
                ->add('success', 'Your email has been successfully sent');

            $response = new JsonResponse();
            $response->setData(array('status' => 'success'));

        } else
        {
            $this->get('session')
                ->getFlashBag()
                ->add('danger', 'Your email has not been forwarded');

            $response = new JsonResponse();
            $response->setData(array('status' => 'failure'));
        }

        return $response;

    }

}
