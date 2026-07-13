<?php

namespace App\Repository;

use App\Entity\Beatmapset;
use App\Entity\BeatmapsetComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BeatmapsetComment>
 */
class BeatmapsetCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BeatmapsetComment::class);
    }

    public function findByBeatmapset(Beatmapset $beatmapset): array
    {
        return $this->createQueryBuilder('c')
            ->addSelect('a')
            ->innerJoin('c.author', 'a')
            ->andWhere('c.beatmapset = :beatmapset')
            ->setParameter('beatmapset', $beatmapset)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByBeatmapsetOrderedByLikes(Beatmapset $beatmapset): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.beatmapset = :beatmapset')
            ->setParameter('beatmapset', $beatmapset)
            ->orderBy('c.likes', 'DESC')
            ->addOrderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return BeatmapsetComment[] Returns an array of BeatmapsetComment objects
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

//    public function findOneBySomeField($value): ?BeatmapsetComment
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
