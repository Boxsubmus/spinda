<?php

namespace App\Serializer;

use App\Entity\Beatmapset;
use App\Service\StorageService;

class BeatmapsetSerializer
{
    public static function serializeVerbose(Beatmapset $beatmapset, StorageService $storage): array
    {
        $author = $beatmapset->getAuthor();

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
            'likes' => $beatmapset->getLikes(),
            'dislikes' => $beatmapset->getDislikes(),
            'downloads' => $beatmapset->getDownloads(),
            'favorites' => $beatmapset->getFavorites(),
            'featured' => $beatmapset->isFeatured()
        ];
    }
}