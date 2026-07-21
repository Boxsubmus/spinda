<?php

namespace App\Service;

final class CoverResult
{
    /** @param array<string, CoverVariant> $variants keyed by 'list', 'card', 'original' */
    public function __construct(
        public readonly array $variants,
    ) {
    }

    public function get(string $name): CoverVariant
    {
        return $this->variants[$name] ?? throw new \RuntimeException("Unknown cover variant: {$name}");
    }
}