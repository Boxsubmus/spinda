<?php

namespace App\EventListener;

use Nytodev\InertiaBundle\Service\Inertia;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class InertiaShareListener
{
    public function __construct(
        private readonly Inertia $inertia,
        private readonly Security $security,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
    ) {}

    public function onKernelRequest(RequestEvent $event, ): void
    {
        /** @var \App\Entity\User|null $user */
        $user = $this->security->getUser();
        $userString = null;

        if ($user != null) {
            $userString =[
                'id' => $user->getId(),
                'steamId' => $user->getSteamid(),
                'username' => $user->getUsername(),
                'avatarURL' => $user->getAvatarUrl()
            ];
        }

        $csrfTokenManager = $this->csrfTokenManager;

        $this->inertia->share('auth', [
            'user' => $userString
        ]);
        $this->inertia->share('csrfToken', function () use ($csrfTokenManager) {
            return $csrfTokenManager->getToken('inertia')->getValue();
        });
    }
}