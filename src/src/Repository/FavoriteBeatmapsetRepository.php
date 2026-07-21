<?php

namespace App\Repository;

use App\Entity\Beatmapset;
use App\Entity\FavoriteBeatmapset;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FavoriteBeatmapset>
 */
class FavoriteBeatmapsetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FavoriteBeatmapset::class);
    }

    public function isFavorited(User $user, Beatmapset $beatmapset): bool
    {
        return $this->createQueryBuilder('f')
            ->select('1')
            ->andWhere('f.user = :user')
            ->andWhere('f.beatmapset = :beatmapset')
            ->setParameter('user', $user)
            ->setParameter('beatmapset', $beatmapset)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult() !== null;
    }

    public function findFavoritedBeatmapsets(User $user): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('b', 'author')
            ->from(Beatmapset::class, 'b')
            ->innerJoin('b.author', 'author')
            ->innerJoin(FavoriteBeatmapset::class, 'f', 'WITH', 'f.beatmapset = b')
            ->andWhere('f.user = :user')
            ->setParameter('user', $user)
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function incrementFavoriteCount(Beatmapset $beatmapset, int $delta): void
    {
        $this->getEntityManager()->createQuery(
            'UPDATE App\Entity\Beatmapset b SET b.favorites = b.favorites + :delta WHERE b.id = :id'
        )
        ->setParameter('delta', $delta)
        ->setParameter('id', $beatmapset->getId())
        ->execute();
    }
}
