<?php

namespace App\Service;

use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ApiTokenService
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function issue(User $user, string $source, ?\DateInterval $ttl = null): ApiToken
    {
        $token = new ApiToken();
        $token->setUser($user);
        $token->setSource($source);
        $token->setToken(bin2hex(random_bytes(32))); // 64-char random token
        $token->setCreatedAt(new \DateTimeImmutable());

        if ($ttl) {
            $token->setExpiresAt((new \DateTimeImmutable())->add($ttl));
        }

        $this->em->persist($token);
        $this->em->flush();

        return $token;
    }

    public function revoke(ApiToken $token): void
    {
        $this->em->remove($token);
        $this->em->flush();
    }

    public function revokeAllForUser(User $user): void
    {
        foreach ($user->getApiTokens() as $token) {
            $this->em->remove($token);
        }
        $this->em->flush();
    }
}