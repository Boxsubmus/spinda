<?php

namespace App\Security;

use App\Repository\ApiTokenRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(private ApiTokenRepository $apiTokenRepository)
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization')
            && str_starts_with($request->headers->get('Authorization'), 'Bearer ');
    }

    public function authenticate(Request $request): Passport
    {
        $rawToken = substr($request->headers->get('Authorization'), 7); // strip "Bearer "
        $hashedToken = hash('sha256', $rawToken);

        $apiToken = $this->apiTokenRepository->findOneBy(['token' => $hashedToken]);

        if (!$apiToken) {
            throw new CustomUserMessageAuthenticationException('Invalid API token.');
        }

        if ($apiToken->isExpired()) {
            throw new CustomUserMessageAuthenticationException('API token expired.');
        }

        $apiToken->setLastUsedAt(new \DateTimeImmutable());
        // flush handled lazily by Doctrine's request-scoped EM, or flush explicitly if you prefer

        return new SelfValidatingPassport(
            new UserBadge($apiToken->getUser()->getSteamid(), fn () => $apiToken->getUser())
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['error' => $exception->getMessage()], 401);
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?Response
    {
        return null; // continue to the controller
    }
}