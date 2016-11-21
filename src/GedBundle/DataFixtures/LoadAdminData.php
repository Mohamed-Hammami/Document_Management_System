<?php
/**
 * Created by PhpStorm.
 * User: Owner
 * Date: 21/11/2016
 * Time: 22:34
 */

namespace GedBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use GedBundle\Entity\User;

class LoadAdminData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $admin = new User();

        $admin->setName('Admin');
        $admin->setEmail('dmsservermail@gmail.com');
        $admin->setRoles(array('ROLE_ADMIN'));
        $admin->setPassword('admin');
        $admin->setEnabled(true);

        $admin->setSurName('One');
        $admin->setName('The');

        $manager->persist($admin);
        $manager->flush();

        $this->addReference('admin-user', $admin);
    }

    public function getOrder()
    {
        return 1;
    }
}