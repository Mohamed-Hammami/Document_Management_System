<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 21/11/2016
 * Time: 22:54
 */

namespace GedBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use GedBundle\Entity\Workspace;

class LoadWorkspaceData extends  AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $workspace = new Workspace();
        $workspace->setUser($this->getReference('admin-user'));

        $manager->persist($workspace);
        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}