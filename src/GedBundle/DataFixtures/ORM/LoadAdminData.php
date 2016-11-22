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
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadAdminData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }


    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        $admin = $userManager->createUser();

        $admin->setUsername('Admin');
        $admin->setRegistrationDate(new \DateTime('now'));
        $admin->setEmail('dmsservermail@gmail.com');
        $admin->setRoles(array('ROLE_ADMIN'));
        $admin->setPlainPassword('admin');
        $admin->setEnabled(true);

        $admin->setSurName('One');
        $admin->setName('The');

        $userManager->updateUser($admin, true);;

        $this->addReference('admin-user', $admin);
    }

    public function getOrder()
    {
        return 1;
    }
}