<?php

namespace App\EventListener;

use App\Entity\UserSession;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

final class SessionTrackingListener
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestStack $requestStack,
    ) {
    }

    #[AsEventListener]
    public function onLoginSuccessEvent(LoginSuccessEvent $event): void
    {
        $session = $this->requestStack->getSession();
        $session->start(); // ensure a session ID actually exists yet

        $userSession = new UserSession();
        $userSession->setSessionId($session->getId());
        $userSession->setUser($event->getUser());
        $userSession->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($userSession);
        $this->em->flush();
    }
}
