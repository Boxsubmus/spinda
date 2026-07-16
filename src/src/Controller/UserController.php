<?php

namespace App\Controller;

use App\Repository\BeatmapsetRepository;
use App\Repository\UserRepository;
use App\Serializer\BeatmapsetSerializer;
use App\Serializer\UserSerializer;
use App\Service\StorageService;
use Nytodev\InertiaBundle\Service\Inertia;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    public function __construct(
        private readonly StorageService $storage,
    ) {}

    #[Route('/users/{id}', name: 'app_user')]
    public function show($id, UserRepository $repository, Inertia $inertia, BeatmapsetRepository $beatmapsRe): Response
    {
        $user = $repository->find($id);
        
        $beatmaps = $beatmapsRe->findBy([
            'author' => $id
        ]);
        $beatmapsData = array_map(function ($beatmap) use ($beatmaps) {
            return BeatmapsetSerializer::serializeVerbose($beatmap, $this->storage);
        }, $beatmaps);

        return $inertia->render('users/Show', [
            'user' => UserSerializer::serializeVerbose($user),
            'beatmaps' => $beatmapsData
        ]);
        /*
        return $this->render('user/index.html.twig', [
            'storage' => $this->storage,
            'user' => $user,
            'beatmaps' => $beatmaps
        ]);
        */
    }
}
