<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 06/04/2016
 * Time: 10:46
 */

namespace GedBundle\Utils;

class FieldDescription
{
    private $label;
    private $type;

    public function __construct($label, $type)
    {
        $this->label = $label;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

}