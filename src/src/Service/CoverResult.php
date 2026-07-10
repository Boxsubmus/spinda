<?php

namespace App\Service;

class CoverResult
{
    public function __construct(
        public readonly string $path,
        public readonly string $url,
        public readonly int $width,
        public readonly int $height,
    ) {}
}