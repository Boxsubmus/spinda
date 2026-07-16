<?php

namespace App\Controller;

use App\Repository\BeatmapsetRepository;
use App\Service\MediaUrlHelper;
use App\Service\StorageService;
use Nytodev\InertiaBundle\Service\Inertia;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly StorageService $storage,
    ) {}

    #[Route('/', name: 'app_home')]
    public function index(Inertia $inertia, BeatmapsetRepository $repository): Response
    {
        /** @var \App\Entity\User|null $user */
        $user = $this->getUser();

        /*
        if ($user) {
            $beatmapSets = $repository->findAllOrderedByNewest();

            return $this->render("index.html.twig", [
                'beatmapsets' => $beatmapSets,
                'storage' => $this->storage,
            ]);
            // return new Response('Logged in as: ' . $user->getAvatarUrl());
        }
*/
        
        return $inertia->render('Home', [
        ]);
        // return $this->render("index.html.twig");
    }
}