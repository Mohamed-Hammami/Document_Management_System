<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 21/05/2016
 * Time: 17:29
 */

namespace GedBundle\EventListener;

use Avanzu\AdminThemeBundle\Event\ShowUserEvent;
use GedBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class NavbarUserListener
{

    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function onShowUser(ShowUserEvent $event) {

        $user = $this->getUser();
        $event->setUser($user);

    }

    protected function getUser()
    {

        if ( $this->tokenStorage->getToken()->isAuthenticated() )
        {
            $user = new User();
            $user = $this->tokenStorage->getToken()->getUser();

            return $user;
        } else {
            throw new AccessDeniedException();
        }


    }



}