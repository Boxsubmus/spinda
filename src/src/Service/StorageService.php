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
        if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png'])) {
            throw new \RuntimeException('Invalid image type. Allowed: JPEG, PNG');
        }

        if ($file->getSize() > 1 * 1024 * 1024 * 10) {
            throw new \RuntimeException('Image too large. Max: 10MB. Image size: ' . $file->getSize());
        }

        $source = $this->loadImage($file->getRealPath());

    $variants = [
        'list' => 128,
        'card' => 256,
    ];

    $results = [];

    // Cropped + resized variants
    foreach ($variants as $name => $size) {
        $square = $this->cropToSquare($source);
        $resized = $this->resizeImage($square, $size, $size);
        imagedestroy($square);

        $path = sprintf('beatmaps/%d/covers/%s.jpg', $beatmapId, $name);
        $data = $this->encodeJpg($resized);
        imagedestroy($resized);

        $this->storage->write($path, $data);

        [$width, $height] = [$size, $size]; // known exactly, no need to re-decode

        $results[$name] = new CoverVariant(
            path: $path,
            url: $this->getPublicUrl($path),
            width: $width,
            height: $height,
        );
    }

    // Original, uncropped, converted to jpg but not resized
    $originalPath = sprintf('beatmaps/%d/covers/original.jpg', $beatmapId);
    $originalData = $this->encodeJpg($source);
    $this->storage->write($originalPath, $originalData);

    $results['original'] = new CoverVariant(
        path: $originalPath,
        url: $this->getPublicUrl($originalPath),
        width: imagesx($source),
        height: imagesy($source),
    );

    imagedestroy($source);

    return new CoverResult($results);
    }

    /**
 * Load an image resource from any supported source format
 */
private function loadImage(string $sourcePath): \GdImage
{
    $mimeType = mime_content_type($sourcePath);

    $image = match ($mimeType) {
        'image/jpeg' => imagecreatefromjpeg($sourcePath),
        'image/webp' => imagecreatefromwebp($sourcePath),
        'image/gif' => imagecreatefromgif($sourcePath),
        'image/png' => $this->loadPngFlattened($sourcePath),
        default => throw new \RuntimeException('Unsupported image type'),
    };

    if (!$image) {
        throw new \RuntimeException('Failed to create image from source');
    }

    return $image;
}

/**
 * PNGs may have transparency; flatten onto white since we're always outputting JPG
 */
private function loadPngFlattened(string $sourcePath): \GdImage|false
{
    $png = imagecreatefrompng($sourcePath);
    if (!$png) {
        return false;
    }

    $width = imagesx($png);
    $height = imagesy($png);
    $flattened = imagecreatetruecolor($width, $height);
    $white = imagecolorallocate($flattened, 255, 255, 255);
    imagefill($flattened, 0, 0, $white);
    imagecopy($flattened, $png, 0, 0, 0, 0, $width, $height);
    imagedestroy($png);

    return $flattened;
}

/**
 * Crop an image to a centered square, using the shorter dimension
 */
private function cropToSquare(\GdImage $source): \GdImage
{
    $width = imagesx($source);
    $height = imagesy($source);
    $side = min($width, $height);

    $srcX = (int) (($width - $side) / 2);
    $srcY = (int) (($height - $side) / 2);

    $cropped = imagecreatetruecolor($side, $side);
    imagecopy($cropped, $source, 0, 0, $srcX, $srcY, $side, $side);

    return $cropped;
}

/**
 * Resize an image to exact target dimensions
 */
private function resizeImage(\GdImage $source, int $targetWidth, int $targetHeight): \GdImage
{
    $resized = imagecreatetruecolor($targetWidth, $targetHeight);
    imagecopyresampled(
        $resized, $source,
        0, 0, 0, 0,
        $targetWidth, $targetHeight,
        imagesx($source), imagesy($source)
    );

    return $resized;
}

/**
 * Encode a GD image as JPG and return the raw bytes
 */
private function encodeJpg(\GdImage $image): string
{
    ob_start();
    imagejpeg($image, null, 80);
    $data = ob_get_clean();

    if ($data === false) {
        throw new \RuntimeException('Failed to convert image to JPG');
    }

    return $data;
}

    /**
     * Delete cover art
     */
    public function deleteCover(int $beatmapId): void
    {
        $names = ['list.jpg', 'card.jpg', 'original.jpg'];
        $dirPath = sprintf('beatmaps/%d/covers', $beatmapId);

        foreach ($names as $name) {
            $path = $dirPath . '/' . $name;
            if ($this->storage->fileExists($path)) {
                $this->storage->delete($path);
            }
        }

        if ($this->storage->directoryExists($dirPath)) {
            $contents = $this->storage->listContents($dirPath)->toArray();
            if (empty($contents)) {
                $this->storage->deleteDirectory($dirPath);
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

    public function fileExists(string $location): bool
    {
        return $this->storage->fileExists($location);
    }

    public function readStream(string $path)
    {
        return $this->storage->readStream($path);
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