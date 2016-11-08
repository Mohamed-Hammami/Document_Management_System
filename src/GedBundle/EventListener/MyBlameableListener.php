<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 07/11/2016
 * Time: 00:04
 */

namespace GedBundle\EventListener;

use Gedmo\Blameable\BlameableListener;

class MyBlameableListener extends BlameableListener
{
    protected function updateField($object, $ea, $meta, $field)
    {
        // If we don't have a user, we are in a task and set a default-user

        if (null === $this->getFieldValue($meta, $field, $ea)) {
            return;
        }
        parent::updateField($object, $ea, $meta, $field);
    }
}