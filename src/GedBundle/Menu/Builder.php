<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 14/04/2016
 * Time: 13:19
 */

namespace GedBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;


class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options)
    {


        $menu = $factory->createItem('root');

        $menu->addChild('Home')
            ->addChild('Folder1')
            ->addChild('Folder2')
        ;

        return $menu;
    }
}