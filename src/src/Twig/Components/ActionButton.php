<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class ActionButton
{
    public ?string $label = null;
    public string $icon = 'fa-download';

    // Link-specific
    public ?string $url = null;

    // Button-specific
    public string $type = 'button'; // button|submit|reset
    public bool $disabled = false;
}
