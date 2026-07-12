<?php

namespace App\Controller;

use App\Repository\BeatmapsetRepository;
use App\Repository\UserRepository;
use App\Service\StorageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    public function __construct(
        private readonly StorageService $storage,
    ) {}

    #[Route('/users/{id}', name: 'app_user')]
    public function index($id, UserRepository $repository, BeatmapsetRepository $beatmapsRe): Response
    {
        $user = $repository->findOneBy([
            'id' => $id
        ]);
        
        $beatmaps = $beatmapsRe->findBy([
            'author' => $id
        ]);

        return $this->render('user/index.html.twig', [
            'storage' => $this->storage,
            'user' => $user,
            'beatmaps' => $beatmaps
        ]);
    }
}
