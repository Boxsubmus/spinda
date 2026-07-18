<?php

namespace App\DataFixtures;

use App\Entity\Beatmapset;
use App\Entity\User;
use App\Service\StorageService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BeatmapsetFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly StorageService $storage
    ) {}

    public function load(ObjectManager $manager): void
    {
        $this->makeBeatmap("Saitama 2000", "Taiko no Tatsujin", 'saitama-2000.zip', 'saitama-2000-cover.png', $manager);
        $this->makeBeatmap("Spam YTPMV", "KrazedDonut", 'spamytpmv.zip', 'spamytpmv.jpg', $manager);
    }

    private function makeBeatmap(string $title, string $artist, string $zip, string $art, ObjectManager $manager): void
    {
        $user = $this->getReference('user_starpelly', User::class);

        // Create beatmapset (need ID for cover)
        $map = new Beatmapset();
        $map->setTitle($title);
        $map->setArtist( $artist);
        $map->setAuthor($user);
        $map->setFavorites(0);
        $map->setCreatedAt(new \DateTimeImmutable());
        $map->setUpdatedAt(new \DateTimeImmutable());
        $map->setDownloads(0);
        $map->setFeatured(false);

        $beatmapFile = new UploadedFile(
            __DIR__ . '/Files/' . $zip,
            $zip,
            'application/zip',
            null,
            true
        );

        // Store beatmap
        $beatmapResult = $this->storage->storeBeatmap($beatmapFile);
        $map->setFileHash($beatmapResult->hash);
        $map->setFilesize($beatmapResult->size);
        // $map->setFileExtension($beatmapResult->extension);

        $manager->persist($map);
        $manager->flush(); // Flush to get ID

        // Now store cover file using the ID
        $coverFile = new UploadedFile(
            __DIR__ . '/Files/' . $art,
            $art,
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

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
