<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 08/04/2016
 * Time: 16:42
 */

namespace GedBundle\Utils;


class BatchActionDescription
{
    private $name;
    private $confirmation;

    public function __construct($name, $confirmation)
    {
        $this->name = $name;
        $this->confirmation = $confirmation;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param boolean $conf
     */
    public function setConfirmation($conf)
    {
        $this->confirmation = $conf;
    }

    /**
     * @return boolean
     */
    public function hasConfirmation()
    {
        return $this->confirmation;
    }
}