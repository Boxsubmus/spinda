<?php

namespace App\MessageHandler;

use App\Message\PruneSessionsMessage;
use App\Repository\UserSessionRepository;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Doctrine\DBAL\Connection;

#[AsMessageHandler]
class PruneSessionsHandler
{
    public function __construct(private PdoSessionHandler $sessionHandler, private UserSessionRepository $userSessionRepository, private Connection $connection)
    {
    }

    public function __invoke(PruneSessionsMessage $message): void
    {
        $this->sessionHandler->gc((int) ini_get('session.gc_maxlifetime'));
        $this->userSessionRepository->deleteOrphaned($this->connection);
    }
}