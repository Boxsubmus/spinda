<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class HeartbeatController extends AbstractController
{
    #[Route('/api/heartbeat', name: 'api_heartbeat', methods: ['POST'])]
    public function heartbeat(EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $user->setLastSeenAt(new \DateTimeImmutable());
        $em->flush();

        return $this->json(['ok' => true]);
    }
}
