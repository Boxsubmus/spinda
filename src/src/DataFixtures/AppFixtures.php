<?php

namespace App\DataFixtures;

use App\Entity\Beatmapset;
use App\Entity\User;
use App\Service\StorageService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly StorageService $storage
    ) {}

    public function load(ObjectManager $manager): void
    {
        // Create user
        $user = new User();
        $user->setSteamid("76561198799361364");
        $user->setUsername("Starpelly");
        $user->setAvatarUrl("https://avatars.fastly.steamstatic.com/d34123aed6aa056e70725c849cdc0968c8ffc75e_full.jpg");
        $manager->persist($user);

        // Create beatmapset (need ID for cover)
        $map = new Beatmapset();
        $map->setTitle("Saitama 2000");
        $map->setArtist("Taiko no Tatsujin");
        $map->setAuthor($user);
        $map->setLikes(0);
        $map->setDislikes(0);
        $map->setFavorites(0);

        $beatmapFile = new UploadedFile(
            __DIR__ . '/Files/saitama-2000.zip',
            'saitama-2000.zip',
            'application/zip',
            null,
            true
        );

        // Store beatmap
        $beatmapResult = $this->storage->storeBeatmap($beatmapFile);
        $map->setFileHash($beatmapResult->hash);
        // $map->setFileExtension($beatmapResult->extension);

        $manager->persist($map);
        $manager->flush(); // Flush to get ID

        // Now store cover file using the ID
        $coverFile = new UploadedFile(
            __DIR__ . '/Files/saitama-2000-cover.png',
            'saitama-2000-cover.png',
            'image/png',
            null,
            true
        );

        // Store cover with ID
        $coverResult = $this->storage->storeCover($coverFile, $map->getId());

        $manager->flush();

        // Output info
        echo "✅ Created beatmapset: {$map->getTitle()}\n";
        echo "   ID: {$map->getId()}\n";
        echo "   Beatmap hash: {$map->getFileHash()}\n";
        echo "   Beatmap URL: {$map->getFileUrl($this->storage)}\n";
        echo "   Cover URL: {$map->getCoverUrl($this->storage)}\n";
        echo "   Cover path: {$coverResult->path}\n";
    }
}