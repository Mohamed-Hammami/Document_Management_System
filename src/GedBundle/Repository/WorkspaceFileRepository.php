<?php

namespace GedBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * WorkspaceFileRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class WorkspaceFileRepository extends EntityRepository
{

    public function findFilesByWorkspace($id)
    {
        $qb = $this->createQueryBuilder('wf')
                ->leftJoin('wf.workspace', 'w')
                ->leftJoin('wf.file', 'f')
                ->setParameter('id', $id)
                ->where('w.id = :id')
                ->addSelect('f');

        return $qb->getQuery()->getResult();
    }

    public function findSubscribedEmail($id)
    {
        $qb = $this->createQueryBuilder('wf')
                ->setParameter('id', $id)
                ->where('wf.file = :id')
                ->andWhere('wf.notification > 0')
                ->leftJoin('wf.workspace', 'w')
                ->leftJoin('w.user', 'u')
                ->addSelect('u.email');

        return $qb->getQuery()->getResult();
    }

}
