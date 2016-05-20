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

    public function findFileIdByFolderId($id)
    {

        $qb = $this->createQueryBuilder('fi')
                ->select('fi.id')
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

    public function findCreators($id)
    {
        $qb = $this->_em->createQueryBuilder()
            ->from('GedBundle:File', 'fi')
            ->from('GedBundle:User', 'usr')
            ->select('usr')
            ->distinct()
            ->where('fi.createdBy = usr.id')
            ->andWhere('fi.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()
            ->getResult();
    }

    public function findTagsName($id)
    {
        $qb = $this->createQueryBuilder('fi')
            ->leftJoin('fi.tags', 'tg')
            ->addSelect('tg')
            ->where('fi.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()
            ->getScalarResult();
    }

}
