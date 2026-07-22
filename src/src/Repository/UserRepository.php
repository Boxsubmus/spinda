<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOneByUsernameCaseInsensitive(string $username): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('LOWER(u.username) = LOWER(:username)')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function paginate(int $page = 1, int $perPage = 10): array
    {
        $qb = $this->createQueryBuilder('u')
            ->orderBy('u.id', 'DESC');

        $query = $qb->getQuery()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $paginator = new Paginator($query, fetchJoinCollection: false);
        $total = count($paginator);

        return [
            'items' => iterator_to_array($paginator),
            'total' => $total,
            'lastPage' => (int) ceil($total / $perPage),
        ];
    }

    public function incrementMappingPointCount(User $user, int $delta): void
    {
        $this->getEntityManager()->createQuery(
            'UPDATE App\Entity\User u SET u.mappingPoints = u.mappingPoints + :delta WHERE u.id = :id'
        )
        ->setParameter('delta', $delta)
        ->setParameter('id', $user->getId())
        ->execute();
    }

    public function incrementKudosCount(User $user, int $delta): int
    {
        $this->getEntityManager()->getConnection()->executeStatement(
            'UPDATE user SET kudos = kudos + :delta WHERE id = :id',
            ['delta' => $delta, 'id' => $user->getId()]
        );

        return (int) $this->getEntityManager()->getConnection()->fetchOne(
            'SELECT kudos FROM user WHERE id = :id',
            ['id' => $user->getId()]
        );
    }

    public function getMappingRank(User $user): int
    {
        $count = $this->getEntityManager()->getConnection()->fetchOne(
            'SELECT COUNT(*) FROM user WHERE mapping_points > :mp',
            ['mp' => $user->getMappingPoints()]
        );

        return (int) $count + 1;
    }

    public function getKudosRank(User $user): int
    {
        $count = $this->getEntityManager()->getConnection()->fetchOne(
            'SELECT COUNT(*) FROM user WHERE kudos > :kudos',
            ['kudos' => $user->getKudos()]
        );

        return (int) $count + 1;
    }
}
