<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ApiTokenService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class SteamAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
        private UrlGeneratorInterface $urlGenerator,
        private ApiTokenService $apiTokenService,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'auth_steam_check';
    }

    public function authenticate(Request $request): Passport
    {
        $params = $request->query->all();

        if (($params['openid_mode'] ?? null) !== 'id_res') {
            throw new AuthenticationException('Steam login failed.');
        }

        $verifyParams = [];
        foreach ($params as $key => $value) {
            $openidKey = str_replace('openid_', 'openid.', $key);
            $verifyParams[$openidKey] = $value;
        }
        $verifyParams['openid.mode'] = 'check_authentication';

        $client = HttpClient::create();
        $verifyResponse = $client->request('POST', 'https://steamcommunity.com/openid/login', [
            'body' => $verifyParams,
        ]);

        if (!str_contains($verifyResponse->getContent(), 'is_valid:true')) {
            throw new AuthenticationException('Steam verification failed.');
        }

        $claimedId = $params['openid_claimed_id'] ?? '';
        if (!preg_match('#/id/(\d+)$#', $claimedId, $matches)) {
            throw new AuthenticationException('Could not extract SteamID.');
        }
        $steamId64 = $matches[1];

        $profileResponse = $client->request('GET', 'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/', [
            'query' => [
                'key' => $_ENV['STEAM_API_KEY'],
                'steamids' => $steamId64,
            ],
        ]);
        $player = $profileResponse->toArray()['response']['players'][0] ?? [];

        return new SelfValidatingPassport(
            new UserBadge($steamId64, function (string $steamId64) use ($player) {
                $user = $this->userRepository->findOneBy(['steamid' => $steamId64]);

                if (!$user) {
                    $user = new User();
                    $user->setSteamid($steamId64);
                }

                $user->setUsername($player['personaname'] ?? $steamId64);
                $user->setAvatarUrl($player['avatarfull'] ?? null);

                $this->em->persist($user);
                $this->em->flush();

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();
        $apiToken = $this->apiTokenService->issue($user, 'web', new \DateInterval('P30D'));

        // stash it somewhere your Twig layout can read on the next page load
        $request->getSession()->set('current_api_token', $apiToken->getToken());

        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response('Steam authentication failed: ' . $exception->getMessage(), 403);
    }
}