<?php

namespace GedBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * FileRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FileRepository extends EntityRepository
{

    public function findByFolderId($id)
    {

        $qb = $this->createQueryBuilder('fi')
                ->leftJoin('fi.folder', 'fo')
                ->where('fo.id = :id')
                ->setParameter('id', $id);

        return $qb->getQuery()
                    ->getResult();

    }

    public function findFileUser($id)
    {
        $qb = $this->createQueryBuilder('fi')
                ->leftJoin('fi.folder', 'fo')
                ->where('fo.id = :id')
                ->leftJoin('fi.createdBy', 'crt')
                ->leftJoin('fi.updatedBy', 'upd')
                ->addSelect('crt')
                ->addSelect('upd')
                ->setParameter('id', $id);

        return $qb->getQuery()
                    ->getArrayResult();
    }

    public function findFileComment($id)
    {
        $qb = $this->createQueryBuilder('fi')
            ->where('fi.id = :id')
            ->leftJoin('fi.comments', 'co')
            ->addSelect('co')
            ->setParameter('id', $id);

        return $qb->getQuery()
                   ->getOneOrNullResult();
    }

    public function findCreators($id)
    {
        $qb = $this->_em->createQueryBuilder()
            ->from('GedBundle:File', 'fe')
            ->from('GedBundle:User', 'usr')
            ->select('usr')
            ->distinct()
            ->where('fe.createdBy = usr.id')
            ->andWhere('fe.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()
            ->getResult();
    }

}
