<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserSession;
use App\Enum\SteamAuthResult;
use App\Repository\UserRepository;
use App\Security\SteamTicketAuthenticator;
use App\Service\GeoIpService;
use App\Service\SessionTrackingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class SteamClientAuthController extends AbstractController
{
    public function __construct(
        private \Symfony\Contracts\HttpClient\HttpClientInterface $httpClient,
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
        private CsrfTokenManagerInterface $csrfTokenManager,
        private UserAuthenticatorInterface $userAuthenticator,
        private SteamTicketAuthenticator $steamTicketAuthenticator,
        private GeoIpService $geoIpService,
        private SessionTrackingService $sessionTrackingService
    ) {
    }

    private function verifyTicket(string $ticket): ?string
    {
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
            return null;
        }

        return $params['steamid'];
    }

    private function logIn(Request $request, User $user): JsonResponse
    {
        $this->userAuthenticator->authenticateUser($user, $this->steamTicketAuthenticator, $request);

        // Update last seen at when opening the game as that counts as "online"
        $user->setLastSeenAt(new \DateTimeImmutable());
        $this->em->persist($user);

        // Regenerate the session ID to prevent fixation and avoid PK collisions
        // in UserSession when a still-valid cookie survives a relaunch.
        $session = $request->getSession();
        $oldSessionId = $session->getId();
        $session->migrate(true);
        $newSessionId = $session->getId();
        
        $this->sessionTrackingService->track($oldSessionId, $newSessionId, $user, "game");

        return $this->json([
            'result' => SteamAuthResult::SUCCESS->value,
            'username' => $user->getUsername(),
            'csrf_token' => $this->csrfTokenManager->getToken('vote')->getValue(),
        ]);
    }

    #[Route('/api/auth/steam-client/check', name: 'api_auth_steam_client_check', methods: ['POST'])]
    public function check(Request $request): JsonResponse
    {
        $ticket = $request->request->get('ticket');
        if (!$ticket) {
            return $this->json(['result' => SteamAuthResult::INVALID_TICKET->value], 400);
        }

        $steamId64 = $this->verifyTicket($ticket);
        if (!$steamId64) {
            return $this->json(['result' => SteamAuthResult::INVALID_TICKET->value], 401);
        }

        $user = $this->userRepository->findOneBy(['steamid' => $steamId64]);

        if (!$user) {
            $profileResponse = $this->httpClient->request('GET', 'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/', [
                'query' => ['key' => $_ENV['STEAM_API_KEY'], 'steamids' => $steamId64],
            ]);
            $player = $profileResponse->toArray()['response']['players'][0] ?? [];

            return $this->json([
                'result' => SteamAuthResult::NO_ACCOUNT->value,
                'suggested_username' => $player['personaname'] ?? null,
            ]);
        }

        return $this->logIn($request, $user);
    }

    #[Route('/api/auth/steam-client/register', name: 'api_auth_steam_client_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $ticket = $request->request->get('ticket');
        $username = trim((string) $request->request->get('username'));

        if (!$ticket || $username === '') {
            return $this->json(['result' => SteamAuthResult::INVALID_TICKET->value], 400);
        }

        if (!$this->validateUsername($username)) {
            return $this->json(['result' => SteamAuthResult::USERNAME_INVALID->value], 400);
        }

        $steamId64 = $this->verifyTicket($ticket);
        if (!$steamId64) {
            return $this->json(['result' => SteamAuthResult::INVALID_TICKET->value], 401);
        }

        $existing = $this->userRepository->findOneBy(['steamid' => $steamId64]);
        if ($existing) {
            return $this->logIn($request, $existing);
        }

        if ($this->userRepository->findOneByUsernameCaseInsensitive($username)) {
            return $this->json(['result' => SteamAuthResult::USERNAME_TAKEN->value], 409);
        }

        $profileResponse = $this->httpClient->request('GET', 'https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/', [
            'query' => ['key' => $_ENV['STEAM_API_KEY'], 'steamids' => $steamId64],
        ]);
        $player = $profileResponse->toArray()['response']['players'][0] ?? [];

        $user = new User();
        $user->setSteamid($steamId64);
        $user->setUsername($username);
        $user->setAvatarUrl($player['avatarfull'] ?? null);
        $user->setCreatedAt(new \DateTimeImmutable());

        $ip = $request->getClientIp();
        $countryCode = $this->geoIpService->getCountryCode($ip);
        if (!$countryCode) {
            $countryCode = 'XX';
        }
        $user->setCountryAcronym($countryCode);

        $user->setMappingPoints(0);
        $user->setLastSeenAt(new \DateTimeImmutable());

        $this->em->persist($user);
        $this->em->flush();

        return $this->logIn($request, $user);
    }

    private function validateUsername(string $username): bool
    {
        // 2+ characters, letters/numbers/underscores only
        return (bool) preg_match('/^[a-zA-Z0-9_]{2,20}$/', $username);
    }
}