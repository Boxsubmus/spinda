<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\GeoIpService;
use Doctrine\ORM\EntityManagerInterface;
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
        private CsrfTokenManagerInterface $csrfTokenManager,
        private EntityManagerInterface $em,
        private GeoIpService $geoIpService
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

        return new SelfValidatingPassport(
            new UserBadge($steamId64, function (string $steamId64) use ($params, $request) {
                $user = $this->userRepository->findOneBy(['steamid' => $steamId64]);
                if (!$user) {
                    // fetch profile info to populate the new account
                    $profileResponse = $this->httpClient->request('GET', 'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/', [
                        'query' => [
                            'key' => $_ENV['STEAM_API_KEY'],
                            'steamids' => $steamId64,
                        ],
                    ]);
                    
                    $player = $profileResponse->toArray()['response']['players'][0] ?? [];
                    $user = new User();
                    $user->setSteamid($steamId64);
                    $user->setUsername($player['personaname'] ?? $steamId64);
                    $user->setAvatarUrl($player['avatarfull'] ?? null);
                    $user->setCreatedAt(new \DateTimeImmutable());

                    $countryCode = $this->geoIpService->getCountryCode($request->getClientIp());
                    if (!$countryCode) {
                        $countryCode = 'XX';
                    }
                    $user->setCountryAcronym($countryCode);

                    $user->setMappingPoints(0);

                    $this->em->persist($user);
                    $this->em->flush();
                }
                return $user;
            })
        );
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