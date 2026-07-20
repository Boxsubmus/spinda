<?php

namespace App\Model;

use App\Entity\Beatmapset;
use App\Service\StorageService;
use Symfony\Component\HttpFoundation\Exception\ExceptionInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BeatmapsetArchive
{
    private string $localPath;
    private \ZipArchive $zip;
    private int|bool $errorCode;

    public function __construct(string $localPath, private ?Beatmapset $beatmapset = null)
    {
        $this->localPath = $localPath;
        $this->zip = new \ZipArchive();
        $this->errorCode = $this->zip->open($this->localPath);
    }

    public static function fetch(Beatmapset $beatmapset, StorageService $storage): ?static
    {
        $path = $beatmapset->getPackagePath($storage); // Flysystem path, e.g. beatmap_files/ab/cd/{hash}.zip

        if (empty($path) || !$storage->fileExists($path)) {
            return null;
        }

        $tmpPath = tempnam(sys_get_temp_dir(), 'spinzip_');
        if ($tmpPath === false) {
            return null;
        }

        try {
            $sourceStream = $storage->readStream($path);
            $destStream = fopen($tmpPath, 'wb');
            stream_copy_to_stream($sourceStream, $destStream);
            fclose($sourceStream);
            fclose($destStream);
        } catch (\Throwable $e) {
            @unlink($tmpPath);
            return null;
        }

        $archive = new static($tmpPath, $beatmapset);

        if ($archive->errorCode !== true) {
            $archive->close();
            @unlink($tmpPath);
            return null;
        }

        return $archive;
    }

    public function scanForCover(): ?string
    {
        $candidates = ['art.png', 'art.jpg', 'art.jpeg'];

        for ($i = 0; $i < $this->zip->numFiles; $i++) {
            $name = $this->zip->getNameIndex($i);
            if ($name === false) {
                continue;
            }
            if (in_array(strtolower(basename($name)), $candidates, true)) {
                return $name;
            }
        }

        return null;
    }

    public function extractCover(): ?UploadedFile
    {
        $entry = $this->scanForCover();
        if ($entry === null) {
            return null;
        }

        $contents = $this->zip->getFromName($entry);
        if ($contents === false) {
            return null;
        }

        $extension = strtolower(pathinfo($entry, PATHINFO_EXTENSION)) ?: 'jpg';
        $base = tempnam(sys_get_temp_dir(), 'cover_');
        $tmpPath = $base . '.' . $extension;
        rename($base, $tmpPath);
        file_put_contents($tmpPath, $contents);

        return new UploadedFile(
            $tmpPath,
            basename($entry),
            mime_content_type($tmpPath) ?: null,
            null,
            true
        );
    }

    public function close(): void
    {
        $this->zip->close();
    }

    public function __destruct()
    {
        @$this->zip->close();
        // This is always a temp copy now (downloaded from Flysystem), safe to delete
        if (is_file($this->localPath)) {
            @unlink($this->localPath);
        }
    }
}