<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/users/{id}', name: 'app_user')]
    public function index($id, UserRepository $repository): Response
    {
        $user = $repository->findOneBy([
            'id' => $id
        ]);

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user
        ]);
    }
}
