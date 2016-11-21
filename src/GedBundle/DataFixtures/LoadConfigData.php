<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 21/11/2016
 * Time: 22:34
 */

namespace GedBundle\DataFixtures;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use GedBundle\Entity\Config;


class LoadConfigData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $config = new Config();

        $config->setFirstTime(true);



        $manager->persist($config);
        $manager->flush();
    }
}