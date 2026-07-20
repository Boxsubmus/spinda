<?php

namespace App\Entity;

use App\Repository\BeatmapsetFileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BeatmapsetFileRepository::class)]
class BeatmapsetFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $fileSize = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $sha2Hash = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function setFileSize(int $fileSize): static
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    public function getSha2Hash(): ?string
    {
        return $this->sha2Hash;
    }

    public function setSha2Hash(string $sha2Hash): static
    {
        $this->sha2Hash = $sha2Hash;

        return $this;
    }
}
