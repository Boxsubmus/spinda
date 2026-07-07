<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        /** @var \App\Entity\User|null $user */
        $user = $this->getUser();

        if ($user) {
            return new Response('Logged in as: ' . $user->getAvatarUrl());
        }

        return new Response('<a href="/auth/steam">Log in with Steam</a>');
    }
}