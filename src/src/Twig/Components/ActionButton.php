<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class ActionButton
{
    public string $url;
    public string $label = 'Download';
    public string $icon = 'fa-download';
}
