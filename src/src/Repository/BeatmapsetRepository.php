<?php

namespace App\Repository;

use App\Entity\Beatmapset;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Beatmapset>
 */
class BeatmapsetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Beatmapset::class);
    }

    public function findAllOrderedByNewest(): array
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Beatmapset[] Returns an array of Beatmapset objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Beatmapset
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
