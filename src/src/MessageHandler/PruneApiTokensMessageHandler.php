<?php

namespace App\MessageHandler;

use App\Message\PruneApiTokensMessage;
use App\Repository\ApiTokenRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PruneApiTokensMessageHandler
{
    public function __construct(private ApiTokenRepository $repository)
    {
    }

    public function __invoke(PruneApiTokensMessage $message): void
    {
        $this->repository->deleteExpired();
    }
}
