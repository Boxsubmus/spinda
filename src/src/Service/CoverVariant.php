<?php

namespace App\Service;

final class CoverVariant
{
    public function __construct(
        public readonly string $path,
        public readonly string $url,
        public readonly int $width,
        public readonly int $height,
    ) {
    }
}