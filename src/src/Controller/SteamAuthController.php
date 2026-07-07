<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SteamAuthController extends AbstractController
{
    #[Route('/auth/steam', name: 'auth_steam_start')]
    public function start(UrlGeneratorInterface $urlGenerator): RedirectResponse
    {
        $returnUrl = $urlGenerator->generate('auth_steam_check', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $realm = $urlGenerator->generate('app_home', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $params = [
            'openid.ns' => 'http://specs.openid.net/auth/2.0',
            'openid.mode' => 'checkid_setup',
            'openid.return_to' => $returnUrl,
            'openid.realm' => $realm,
            'openid.identity' => 'http://specs.openid.net/auth/2.0/identifier_select',
            'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
        ];

        return new RedirectResponse('https://steamcommunity.com/openid/login?' . http_build_query($params));
    }

    #[Route('/auth/steam/check', name: 'auth_steam_check')]
    public function check(): Response
    {
        // Intercepted by SteamAuthenticator before this ever runs
        throw $this->createAccessDeniedException();
    }
}