<?php

namespace App\Serializer;

use App\Entity\BeatmapDifficulty;
use App\Entity\Beatmapset;
use App\Service\StorageService;

class BeatmapsetSerializer
{
    public static function serializeVerbose(Beatmapset $beatmapset, StorageService $storage): array
    {
        $author = $beatmapset->getAuthor();

        $difficultiesData = [];
        foreach ($beatmapset->getBeatmapDifficulties() as $diff) {
            $difficultiesData[] = BeatmapsetSerializer::serializeDifficultyVerbose($diff);
        }

        return [
            'id' => $beatmapset->getId(),
            'coverUrl' => $beatmapset->getCoverUrl($storage),
            'title' => $beatmapset->getTitle(),
            'description' => $beatmapset->getDescription(),
            'artist' => $beatmapset->getArtist(),
            'createdAt' => $beatmapset->getCreatedAt(),
            'author' => [
                'id' => $author->getId(),
                'username' => $author->getUsername(),
                'avatarURL' => $author->getAvatarURL()
            ],
            'downloads' => $beatmapset->getDownloads(),
            'favorites' => $beatmapset->getFavorites(),
            'featured' => $beatmapset->isFeatured(),
            'difficulties' => $difficultiesData
        ];
    }

    public static function serializeDifficultyVerbose(BeatmapDifficulty $beatmapDifficulty): array
    {
        return [
            'countTaps' => $beatmapDifficulty->getCountTap(),
            'countHolds' => $beatmapDifficulty->getCountHold(),
            'bpm' => $beatmapDifficulty->getBpm(),
            'name' => $beatmapDifficulty->getName(),
            'color' => $beatmapDifficulty->getColor()
        ];
    }
}