<?php

namespace GedBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{

    public function findUsers()
    {
        $qb = $this->createQueryBuilder('u')
                    ->select('u.id')
                    ->addSelect('u.username as text');

        return $qb->getQuery()->getResult();
    }

    public function findFreeUsers($id)
    {
        $em = $this->getEntityManager();
        $groupRepository = $em->getRepository('GedBundle:Groupe');

        $ids = $groupRepository->findUserIds($id);

        $userIds = array();
        foreach($ids as $id)
        {
            array_push($userIds, $id['id']);
        }

        $users = $this->findAll();

        foreach( $users as $key=>$user)
        {
            if( in_array($user->getId(), $userIds) )
                unset($users[$key]);
        }

        return $users;
    }


}
