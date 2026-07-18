<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

/**
 * @extends ServiceEntityRepository<UserSession>
 */
class UserSessionRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private EntityManagerInterface $em)
    {
        parent::__construct($registry, UserSession::class);
    }

    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('us')
            ->andWhere('us.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * Deletes UserSession rows whose sessionId no longer exists in the
     * real PHP sessions table (i.e. the session expired/was GC'd already).
     */
    public function deleteOrphaned(Connection $connection): int
    {
        return $connection->executeStatement(
            'DELETE FROM user_session WHERE session_id NOT IN (SELECT sess_id FROM sessions)'
        );
    }

    public function deleteOlderThan(string $source, \DateTimeImmutable $cutoff, Connection $connection): int
    {
        $staleSessions = $this->createQueryBuilder('us')
            ->andWhere('us.source = :source')
            ->andWhere('us.createdAt < :cutoff')
            ->setParameter('source', $source)
            ->setParameter('cutoff', $cutoff)
            ->getQuery()
            ->getResult();

        $count = 0;
        foreach ($staleSessions as $userSession) {
            $connection->executeStatement(
                'DELETE FROM sessions WHERE sess_id = :sessId',
                ['sessId' => $userSession->getSessionId()]
            );
            $this->em->remove($userSession);
            $count++;
        }

        $this->em->flush();

        return $count;
    }

//    /**
//     * @return UserSession[] Returns an array of UserSession objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserSession
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
