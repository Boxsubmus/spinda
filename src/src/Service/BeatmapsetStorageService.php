<?php

namespace App\Service;

use App\Entity\Beatmapset;
use App\Model\BeatmapsetArchive;

enum CoverRegenerationResult: int
{
    case Success = 1;
    case FetchFailed = 2;
    case NoCoverFound = 3;
    case StorageFailed = 4;
}

class BeatmapsetStorageService
{
    public function __construct(
        private readonly StorageService $storage,
    ) {
    }

    public function regenerateCovers(Beatmapset $beatmapset): CoverRegenerationResult
    {
        $archive = BeatmapsetArchive::fetch($beatmapset, $this->storage);
        if ($archive === null) {
            return CoverRegenerationResult::FetchFailed;
        }

        $cover = $archive->extractCover();
        if ($cover === null) {
            return CoverRegenerationResult::NoCoverFound;
        }

        try {
            $this->storage->storeCover($cover, $beatmapset->getId());
        } catch (\RuntimeException $e) {
            return CoverRegenerationResult::StorageFailed;
        } finally {
            @unlink($cover->getPathname());
        }

        return CoverRegenerationResult::Success;
    }
}