<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 19/11/2016
 * Time: 18:00
 */

namespace GedBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class AddVersionEvent extends Event
{
    protected $file;
    protected $user;


    public function getFile()
    {
        return $this->file;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function __construct($file, $user)
    {
        $this->file = $file;
        $this->user = $user;
    }
}