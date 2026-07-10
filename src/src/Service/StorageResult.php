<?php

namespace App\Service;

class StorageResult
{
    public function __construct(
        public readonly string $hash,
        public readonly string $path,
        public readonly string $url,
        public readonly string $extension,
        public readonly bool $isDuplicate = false,
        public readonly ?int $size = null,
    ) {}
}