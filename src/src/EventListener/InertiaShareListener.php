<?php

namespace App\EventListener;

use Nytodev\InertiaBundle\Service\Inertia;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final class InertiaShareListener
{
    public function __construct(
        private readonly Inertia $inertia,
        private readonly Security $security,
    ) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        /** @var \App\Entity\User|null $user */
        $user = $this->security->getUser();

        $this->inertia->share('auth', [
            'user' => [
                'id' => $user->getId(),
                'steamId' => $user->getSteamid(),
                'username' => $user->getUsername(),
                'avatarURL' => $user->getAvatarUrl()
            ]
        ]);
    }
}