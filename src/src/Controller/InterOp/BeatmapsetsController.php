<?php

namespace App\Controller\InterOp;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BeatmapsetsController extends AbstractController
{
    #[Route('/beatmapsets/controller/interop', name: 'app_beatmapsets_controller_interop')]
    public function index(): Response
    {
        return $this->render('beatmapsets_controller_interop/index.html.twig', [
            'controller_name' => 'BeatmapsetsControllerInteropController',
        ]);
    }
}
