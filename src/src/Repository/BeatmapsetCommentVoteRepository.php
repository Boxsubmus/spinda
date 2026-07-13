<?php

namespace App\Repository;

use App\Entity\BeatmapsetCommentVote;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BeatmapsetCommentVote>
 */
class BeatmapsetCommentVoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BeatmapsetCommentVote::class);
    }

    public function findUserVotesForComments(User $user, array $comments): array
    {
        if (empty($comments)) {
            return [];
        }

        $votes = $this->createQueryBuilder('v')
            ->andWhere('v.user = :user')
            ->andWhere('v.comment IN (:comments)')
            ->setParameter('user', $user)
            ->setParameter('comments', $comments)
            ->getQuery()
            ->getResult();

        $map = [];
        foreach ($votes as $vote) {
            $map[$vote->getComment()->getId()] = $vote->getType();
        }

        return $map; // [commentId => 1 or -1]
    }

//    /**
//     * @return BeatmapsetCommentVote[] Returns an array of BeatmapsetCommentVote objects
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

//    public function findOneBySomeField($value): ?BeatmapsetCommentVote
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
