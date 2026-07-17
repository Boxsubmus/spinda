<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class SteamTicketAuthenticator extends AbstractAuthenticator
{
    public function supports(Request $request): ?bool
    {
        // Only ever invoked manually via authenticateUser(), never matches a real request directly
        return false;
    }

    public function authenticate(Request $request): Passport
    {
        throw new \LogicException('This authenticator only supports manual authentication via authenticateUser().');
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }
}