<?php

namespace Labstag\Repository;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Labstag\Entity\History;
use Labstag\Entity\User;
use Labstag\Lib\ServiceEntityRepositoryLib;

/**
 * @method History|null find($id, $lockMode = null, $lockVersion = null)
 * @method History|null findOneBy(array $criteria, array $orderBy = null)
 * @method History[]    findAll()
 * @method History[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryRepository extends ServiceEntityRepositoryLib
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, History::class);
    }

    public function findAllActiveByUser(?User $user): ?Query
    {
        if (is_null($user)) {
            return null;
        }

        $dql = $this->createQueryBuilder('p');
        $dql->innerJoin('p.refuser', 'u');
        $dql->where('p.enable=:enable');
        $dql->andWhere('u.id=:iduser');
        $dql->orderBy('p.createdAt', 'DESC');
        $dql->setParameters(
            [
                'iduser' => $user->getId(),
                'enable' => true,
            ]
        );

        return $dql->getQuery();
    }

    public function findAllActive(): Query
    {
        $dql = $this->createQueryBuilder('p');
        $dql->join('p.chapitres', 'c');
        $dql->where('p.enable=:enable AND c.enable=:enable');
        $dql->orderBy('p.updatedAt', 'DESC');
        $dql->setParameters(
            ['enable' => true]
        );

        return $dql->getQuery();
    }

    // /**
    //  * @return History[] Returns an array of History objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?History
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
