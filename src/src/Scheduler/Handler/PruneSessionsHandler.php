<?php

namespace App\Scheduler\Handler;

use App\Repository\UserSessionRepository;
use App\Scheduler\Message\PruneSessionsMessage;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class PruneSessionsHandler
{
    private const WEB_MAX_AGE = '-30 days';
    private const GAME_CLIENT_MAX_AGE = '-1 day';

    public function __construct(
        private Connection $connection,
        private UserSessionRepository $userSessionRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(PruneSessionsMessage $message): void
    {
        // Delete genuinely expired PHP sessions directly via SQL
        $maxLifetime = (int) ini_get('session.gc_maxlifetime');
        $expiredCount = $this->connection->executeStatement(
            'DELETE FROM sessions WHERE sess_time < :cutoff',
            ['cutoff' => time() - $maxLifetime]
        );

        // Then apply source-specific age limits
        $webCount = $this->userSessionRepository->deleteOlderThan('web', new \DateTimeImmutable(self::WEB_MAX_AGE), $this->connection);
        $gameCount = $this->userSessionRepository->deleteOlderThan('game_client', new \DateTimeImmutable(self::GAME_CLIENT_MAX_AGE), $this->connection);

        $this->logger->info('Session pruning completed', [
            'expired_sessions_deleted' => $expiredCount,
            'web_sessions_deleted' => $webCount,
            'game_client_sessions_deleted' => $gameCount,
        ]);
    }
}