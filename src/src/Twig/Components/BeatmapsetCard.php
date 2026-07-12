<?php

namespace App\Twig\Components;

use App\Entity\Beatmapset;
use App\Service\StorageService;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class BeatmapsetCard
{
    public Beatmapset $beatmapset;
    public StorageService $storage;
}
