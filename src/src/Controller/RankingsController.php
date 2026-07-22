<?php

namespace App\Controller;

use Nytodev\InertiaBundle\Service\Inertia;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RankingsController extends AbstractController
{
    #[Route('/rankings/mapping')]
    public function mapping(Inertia $inertia): Response
    {
        return $inertia->render('rankings/Mapping', [
        ]);
    }

    #[Route('/rankings/kudos')]
    public function kudos(Inertia $inertia): Response
    {
        return $inertia->render('rankings/Kudos', [
        ]);
    }
}
