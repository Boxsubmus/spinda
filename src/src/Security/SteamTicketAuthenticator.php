<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\HttpClientInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/// Used for the game client.
class SteamTicketAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private UserRepository $userRepository,
        private \Symfony\Contracts\HttpClient\HttpClientInterface $httpClient,
        private CsrfTokenManagerInterface $csrfTokenManager
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'api_auth_steam_client';
    }

    public function authenticate(Request $request): Passport
    {
        $ticket = $request->request->get('ticket');
        if (!$ticket) {
            throw new CustomUserMessageAuthenticationException('Missing ticket.');
        }

        $response = $this->httpClient->request('GET', 'https://api.steampowered.com/ISteamUserAuth/AuthenticateUserTicket/v1/', [
            'query' => [
                'key' => $_ENV['STEAM_API_KEY'],
                'appid' => $_ENV['STEAM_APP_ID'],
                'ticket' => $ticket,
            ],
        ]);

        $data = $response->toArray(false);
        $params = $data['response']['params'] ?? null;

        if (!$params || ($params['result'] ?? null) !== 'OK') {
            throw new CustomUserMessageAuthenticationException('Invalid Steam ticket.');
        }

        $steamId64 = $params['steamid'];
        $user = $this->userRepository->findOneBy(['steamid' => $steamId64]);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException('No account found for this Steam user.');
        }

        return new SelfValidatingPassport(new UserBadge($steamId64, fn () => $user));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        /** @var \App\Entity\User|null $user */
        $user = $token->getUser();

        return new JsonResponse([
            'username' => $user->getUsername(),
            'csrf_token' => $this->csrfTokenManager->getToken('steam_client')->getValue()
        ]);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['error' => $exception->getMessage()], 401);
    }
}