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

    // 0: raw token, 1: api token
    public function issue(User $user, string $source, ?\DateInterval $ttl = null): array
    {
        $rawToken = bin2hex(random_bytes(32));

        $apiToken = new ApiToken();
        $apiToken->setUser($user);
        $apiToken->setSource($source);
        $apiToken->setToken(hash('sha256', $rawToken)); // store the hash
        $apiToken->setCreatedAt(new \DateTimeImmutable());

        if ($ttl) {
            $apiToken->setExpiresAt((new \DateTimeImmutable())->add($ttl));
        }

        $this->em->persist($apiToken);
        $this->em->flush();

        // Return the RAW token to give to the client — this is the only time it ever exists in plaintext
        return [$rawToken, $apiToken]; // note: change return type; caller needs both entity and raw value if useful
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