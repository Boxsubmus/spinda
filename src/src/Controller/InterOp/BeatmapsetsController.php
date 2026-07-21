<?php

namespace App\Controller\InterOp;

use App\Repository\BeatmapsetRepository;
use App\Service\BeatmapsetStorageService;
use App\Service\StorageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BeatmapsetsController extends AbstractController
{
    public function __construct(
        private readonly StorageService $storage,
    ) {}

    #[Route('/api/_io/index-beatmapset/{id}', methods: ['POST'])]
    public function io_indexBeatmapset($id,
        BeatmapsetRepository $repository,
        Request $request,
        EntityManagerInterface $em,
        BeatmapsetStorageService $beatmapsetStorageService): Response
    {
        $beatmapset = $repository->find($id);
        
        $regenCovers = $beatmapsetStorageService->regenerateCovers($beatmapset);

        return $this->json([
            'regenerate_covers' => $regenCovers,
        ]);
    }
}
