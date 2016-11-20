<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 19/11/2016
 * Time: 18:10
 */

namespace GedBundle\EventListener;


use Doctrine\ORM\EntityManager;
use GedBundle\Event\AddVersionEvent;
use GedBundle\Event\RemoveFileEvent;
use GedBundle\Event\RemoveFolderEvent;
use GedBundle\Event\RemoveVersionEvent;
use GedBundle\Utils\Notifier;

class NotificationListener
{
    protected $notifier;
    protected $em;

    public function __construct(Notifier $notifier, EntityManager $em)
    {
        $this->notifier = $notifier;
        $this->em = $em;
    }

    public function notifyAddVersion(AddVersionEvent $event)
    {
        $workspaceFileRepository = $this->em->getRepository('GedBundle:WorkspaceFile');

        $file = $event->getFile();
        $user = $event->getUser();
        $subscribedEmails = $workspaceFileRepository->findSubscribedEmail($file->getId());

        foreach( $subscribedEmails as $subscribedEmail )
        {
            $this->notifier->notifyFileEmail($file, $user, $subscribedEmail['email'], 'updated');
        }

    }

    public function notifyRemoveVersion(RemoveVersionEvent $event)
    {
        $workspaceFileRepository = $this->em->getRepository('GedBundle:WorkspaceFile');

        $file = $event->getFile();
        $user = $event->getUser();
        $subscribedEmails = $workspaceFileRepository->findSubscribedEmail($file->getId());

        foreach( $subscribedEmails as $subscribedEmail )
        {
            $this->notifier->notifyFileEmail($file, $user, $subscribedEmail['email'], 'updated');
        }

    }

    public function notifyRemoveFile(RemoveFileEvent $event)
    {
        $workspaceFileRepository = $this->em->getRepository('GedBundle:WorkspaceFile');

        $file = $event->getFile();
        $user = $event->getUser();
        $subscribedEmails = $workspaceFileRepository->findSubscribedEmail($file->getId());

        foreach( $subscribedEmails as $subscribedEmail )
        {
            $this->notifier->notifyFileEmail($file, $user, $subscribedEmail['email'], 'deleted');
        }
    }

    public function notifyRemoveFolder(RemoveFolderEvent $event)
    {
        $workspaceFolderRepository = $this->em->getRepository('GedBundle:WorkspaceFolder');
        $folder = $event->getFolder();
        $user = $event->getUser();
        $subscribedEmails = $workspaceFolderRepository->findSubscribedEmail($folder->getId());

        foreach( $subscribedEmails as $subscribedEmail )
        {
            $this->notifier->notifyFolderEmail($folder, $user, $subscribedEmail['email'], 'deleted');
        }

    }

}