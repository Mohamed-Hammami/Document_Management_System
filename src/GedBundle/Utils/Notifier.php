<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 19/11/2016
 * Time: 21:43
 */

namespace GedBundle\Utils;


class Notifier
{
    protected $mailer;
    protected $twig;

    public function __construct(\Swift_Mailer $mailer, $twig)

    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }


    public function notifyFileEmail($file, $user, $email, $action)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('File Notification')
            ->setTo($email)
            ->setFrom("dmsservermail@gmail.com")
            ->setBody(
                $this->twig->render(
                    'GedBundle:Mail:notificationMail.html.twig',
                    array(
                        'type'   => 'file',
                        'user'   => $user,
                        'object'   => $file,
                        'action' => $action
                    )
                )
            );

        $this->mailer->send($message);
    }

    public function notifyFolderEmail($folder, $user, $email, $action)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Folder Notification')
            ->setTo($email)
            ->setFrom("dmsservermail@gmail.com")
            ->setBody(
                $this->renderView(
                    'GedBundle:Mail:notificationMail.html.twig',
                    array(
                        'type'   => 'folder',
                        'user'   => $user,
                        'object'   => $folder,
                        'action' => $action
                    )
                )
            );

        $this->get('mailer')->send($message);
    }


}