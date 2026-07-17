<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserSession;
use Doctrine\ORM\EntityManagerInterface;

class SessionTrackingService
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function track(string $oldSessionId, string $newSessionId, User $user): void
    {
        // Remove the old session's tracking row, if any
        if ($oldSessionId !== $newSessionId) {
            $oldUserSession = $this->em->find(UserSession::class, $oldSessionId);
            if ($oldUserSession) {
                $this->em->remove($oldUserSession);
            }
        }


        $userSession = $this->em->find(UserSession::class, $newSessionId);

        if (!$userSession) {
            $userSession = new UserSession();
            $userSession->setSessionId($newSessionId);
            $userSession->setCreatedAt(new \DateTimeImmutable());
            $this->em->persist($userSession);
        }

        $userSession->setUser($user);
        $this->em->flush();
    }
}