<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 14/04/2016
 * Time: 13:45
 */

namespace GedBundle\Event;

use Sonata\AdminBundle\Event\ConfigureMenuEvent;
use Knp\Menu\Loader\NodeLoader;
use Knp\Menu\MenuFactory;
use Doctrine\ORM\EntityManager;

class MenuBuilderListener
{

     protected $em;

    function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function addMenuItems(ConfigureMenuEvent $event)
    {

        $event->getMenu();

        $folderRepository = $this->em->getRepository('GedBundle:Folder');
        $root = $folderRepository->getRootNodes();

        dump($root);

        $factory = new MenuFactory();
        $loader = new NodeLoader($factory);

        $menu = $loader->load($root[0]);

        dump($menu);

        return $menu;

//        $menu = $event->getMenu();
//
//        dump($menu);
//        dump($event);
//        dump($root);
//
//        $menu->addChild('CustomMenu', array(
//        ))->setLabel('My CustomMenu')
//            ->setAttribute('class', 'treeview');
    }
}