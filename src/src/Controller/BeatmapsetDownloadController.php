<?php

namespace App\Controller;

use App\Entity\Beatmapset;
use App\Repository\BeatmapsetRepository;
use App\Service\StorageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

final class BeatmapsetDownloadController extends AbstractController
{
    #[Route('/maps/{id}/download', methods: ['GET'])]
    public function index(
        $id,
        BeatmapsetRepository $repository,
        StorageService $storage,
    ): Response
    {
        /** @var Beatmapset $beatmapset **/
        $beatmapset = $repository->find($id);

        if ($beatmapset === null) {
            throw $this->createNotFoundException('Beatmapset not found');
        }

        $path = $beatmapset->getPackagePath($storage); // Flysystem path, e.g. beatmap_files/ab/cd/{hash}.zip

        if ($path === null || !$storage->fileExists($path)) {
            throw $this->createNotFoundException('Beatmapset file not found');
        }

        $filename = sprintf(
            '%s - %s.zip',
            $this->sanitizeFilename($beatmapset->getArtist()),
            $this->sanitizeFilename($beatmapset->getTitle()),
        );

        $response = new StreamedResponse(function () use ($storage, $path) {
            $stream = $storage->readStream($path);
            fpassthru($stream);
            fclose($stream);
        });

        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set(
            'Content-Disposition',
            $response->headers->makeDisposition(
                'attachment',
                $filename,
                // fallback ascii filename for older browsers/clients
                preg_replace('/[^\x20-\x7E]/', '_', $filename)
            )
        );

        $size = $storage->fileSize($path);
        if ($size !== null) {
            $response->headers->set('Content-Length', (string) $size);
        }

        return $response;
    }

    private function sanitizeFilename(string $name): string
    {
        // Strip characters that are invalid/awkward in filenames across OSes
        return trim(preg_replace('/[\\\\\/:*?"<>|]/', '', $name));
    }
}
