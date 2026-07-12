<?php

namespace App\Controller;

use App\Repository\BeatmapsetRepository;
use App\Service\StorageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BeatmapsetsController extends AbstractController
{
    public function __construct(
        private readonly StorageService $storage,
    ) {}

    #[Route('/beatmapsets/{id}', name: 'app_beatmapsets')]
    public function index($id, BeatmapsetRepository $repository): Response
    {
        $beatmapset = $repository->findOneBy([
            'id' => $id
        ]);

        return $this->render('beatmapsets/index.html.twig', [
            'storage' => $this->storage,
            'controller_name' => 'BeatmapsetsController',
            'beatmapset' => $beatmapset
        ]);
    }

    #[Route('/beatmapsets/{id}/download', name: 'app_beatmapsets_download')]
    public function download($id, BeatmapsetRepository $repository): Response
    {
        return new Response('what');
    }
}
