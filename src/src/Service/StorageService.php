<?php

namespace App\Service;

use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class StorageService
{
    private const FILE_TYPES = [
        'beatmap' => [
            'directory' => 'beatmaps',
            'allowed_extensions' => ['zip'],
            'max_size' => 15 * 1024 * 1024, // 10MB
        ],
    ];

    public function __construct(
        private readonly FilesystemOperator $storage,
    ) {
    }

    /**
     * Store a beatmap file using hash-based storage
     */
    public function storeBeatmap(UploadedFile $file): StorageResult
    {
        $config = self::FILE_TYPES['beatmap'];
        $extension = $file->guessExtension() ?: 'zip';
        
        $this->validateFile($file, $config, $extension);

        $hash = hash_file('sha256', $file->getRealPath());
        $path = $this->buildHashPath($hash, 'beatmap_files', $extension);

        // Check for duplicate
        if ($this->storage->fileExists($path)) {
            return new StorageResult(
                hash: $hash,
                path: $path,
                url: $this->getPublicUrl($path),
                extension: $extension,
                isDuplicate: true,
                size: $file->getSize(),
            );
        }

        $stream = fopen($file->getRealPath(), 'r+');
        $this->storage->writeStream($path, $stream);
        fclose($stream);

        return new StorageResult(
            hash: $hash,
            path: $path,
            url: $this->getPublicUrl($path),
            extension: $extension,
            isDuplicate: false,
            size: $file->getSize(),
        );
    }

    /**
     * Store cover art in human-readable format
     * Auto-converts to JPG
     */
    public function storeCover(UploadedFile $file, int $beatmapId): CoverResult
    {
        // Validate image
        if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp', 'image/gif'])) {
            throw new \RuntimeException('Invalid image type. Allowed: JPEG, PNG, WEBP, GIF');
        }

        if ($file->getSize() > 1 * 1024 * 1024) {
            throw new \RuntimeException('Image too large. Max: 1MB');
        }

        // Generate path: /beatmaps/{id}/covers/list.jpg
        $path = sprintf('beatmaps/%d/covers/list.jpg', $beatmapId);

        // Convert and optimize image to JPG
        $imageData = $this->convertToJpg($file->getRealPath());

        // Store with Flysystem
        $this->storage->write($path, $imageData);

        // Get image dimensions from the converted data
        $tempPath = tempnam(sys_get_temp_dir(), 'img_');
        file_put_contents($tempPath, $imageData);
        list($width, $height) = getimagesize($tempPath);
        unlink($tempPath);

        return new CoverResult(
            path: $path,
            url: $this->getPublicUrl($path),
            width: $width,
            height: $height,
        );
    }

    /**
     * Delete cover art
     */
    public function deleteCover(int $beatmapId): void
    {
        $path = sprintf('beatmaps/%d/covers/list.jpg', $beatmapId);
        if ($this->storage->fileExists($path)) {
            $this->storage->delete($path);
            
            // Check if directory is empty and delete it if it is
            $dirPath = sprintf('beatmaps/%d/covers', $beatmapId);
            if ($this->storage->directoryExists($dirPath)) {
                $contents = $this->storage->listContents($dirPath)->toArray();
                if (empty($contents)) {
                    $this->storage->deleteDirectory($dirPath);
                }
            }
        }
    }

    /**
     * Convert image to JPG and return the image data as a string
     */
    private function convertToJpg(string $sourcePath): string
    {
        // Create image resource based on source type
        $mimeType = mime_content_type($sourcePath);
        
        switch ($mimeType) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $png = imagecreatefrompng($sourcePath);
                $width = imagesx($png);
                $height = imagesy($png);
                $image = imagecreatetruecolor($width, $height);
                $white = imagecolorallocate($image, 255, 255, 255);
                imagefill($image, 0, 0, $white);
                imagecopy($image, $png, 0, 0, 0, 0, $width, $height);
                imagedestroy($png);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($sourcePath);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($sourcePath);
                break;
            default:
                throw new \RuntimeException('Unsupported image type');
        }

        if (!$image) {
            throw new \RuntimeException('Failed to create image from source');
        }

        // Capture the JPG output to a variable
        ob_start();
        imagejpeg($image, null, 80);
        $data = ob_get_clean();
        imagedestroy($image);

        if ($data === false) {
            throw new \RuntimeException('Failed to convert image to JPG');
        }

        return $data;
    }

    /**
     * Build hash-based storage path
     */
    private function buildHashPath(string $hash, string $type, string $extension): string
    {
        $level1 = substr($hash, 0, 2);
        $level2 = substr($hash, 2, 2);
        return sprintf('%s/%s/%s/%s.%s', $type, $level1, $level2, $hash, $extension);
    }

    /**
     * Get public URL
     */
    public function getPublicUrl(string $path): string
    {
        return $this->storage->publicUrl($path);
        // return $this->baseUrl . '/' . ltrim($path, '/');
    }

    /**
     * Get URL from hash, type, and extension
     */
    public function getUrlFromHash(string $hash, string $type, string $extension): string
    {
        $path = $this->buildHashPath($hash, $type, $extension);
        return $this->getPublicUrl($path);
    }

    /**
     * Validate file
     */
    private function validateFile(UploadedFile $file, array $config, string $extension): void
    {
        if (!in_array(strtolower($extension), $config['allowed_extensions'])) {
            throw new \RuntimeException(
                sprintf('Invalid file extension. Allowed: %s', 
                    implode(', ', $config['allowed_extensions'])
                )
            );
        }

        if ($file->getSize() > $config['max_size']) {
            throw new \RuntimeException(
                sprintf('File too large. Max: %d bytes', $config['max_size'])
            );
        }
    }
}