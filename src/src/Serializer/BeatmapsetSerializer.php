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

        $listCoverUrl = $storage->getPublicUrl(sprintf('beatmaps/%d/covers/list.jpg', $beatmapset->getId()));
        $cardCoverUrl = $storage->getPublicUrl(sprintf('beatmaps/%d/covers/card.jpg', $beatmapset->getId()));

        return [
            'id' => $beatmapset->getId(),
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
            'featured' => $beatmapset->getIsFeatured(),
            'difficulties' => $difficultiesData,

            'images' => [
                'list' => $listCoverUrl,
                'card' => $cardCoverUrl
            ]
        ];
    }

    public static function serializeDifficultyVerbose(BeatmapDifficulty $beatmapDifficulty): array
    {
        return [
            'countTaps' => $beatmapDifficulty->getCountTap(),
            'countHolds' => $beatmapDifficulty->getCountHold(),
            'bpm' => $beatmapDifficulty->getBpm(),
            'drainTime' => $beatmapDifficulty->getTotalLength(),
            'name' => $beatmapDifficulty->getName(),
            'color' => $beatmapDifficulty->getColor()
        ];
    }
}