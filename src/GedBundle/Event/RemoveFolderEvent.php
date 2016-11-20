<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 19/11/2016
 * Time: 19:48
 */

namespace GedBundle\Event;


use Symfony\Component\EventDispatcher\Event;

class RemoveFolderEvent extends Event
{
    protected $folder;
    protected $user;


    public function getFolder()
    {
        return $this->folder;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function __construct($folder, $user)
    {
        $this->folder = $folder;
        $this->user = $user;
    }
}