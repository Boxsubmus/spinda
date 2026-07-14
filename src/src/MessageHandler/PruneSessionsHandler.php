<?php

namespace App\Scheduler\Handler;

use App\Scheduler\Message\PruneSessionsMessage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class PruneSessionsHandler
{
    public function __construct(private PdoSessionHandler $sessionHandler)
    {
    }

    public function __invoke(PruneSessionsMessage $message): void
    {
        $this->sessionHandler->gc((int) ini_get('session.gc_maxlifetime'));
    }
}