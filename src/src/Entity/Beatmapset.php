<?php

namespace App\Entity;

use App\Repository\BeatmapsetRepository;
use App\Service\StorageService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BeatmapsetRepository::class)]
class Beatmapset
{
    const STATES = [
        'archive' => 0,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'beatmapsets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $artist = null;

    #[ORM\Column(length: 64, unique: true, nullable: false)]
    private ?string $fileHash = null;

    #[ORM\Column]
    private ?int $favorites = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $approvedAt = null;

    #[ORM\Column]
    private ?int $downloads = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $filesize = null;

    /**
     * @var Collection<int, BeatmapsetComment>
     */
    #[ORM\OneToMany(targetEntity: BeatmapsetComment::class, mappedBy: 'beatmapset', orphanRemoval: true)]
    private Collection $beatmapsetComments;

    #[ORM\Column]
    private ?bool $featured = null;

    public function __construct()
    {
        $this->beatmapsetComments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getArtist(): ?string
    {
        return $this->artist;
    }

    public function setArtist(string $artist): static
    {
        $this->artist = $artist;

        return $this;
    }

    public function getFileHash(): ?string
    {
        return $this->fileHash;
    }

    public function setFileHash(string $fileHash): static
    {
        $this->fileHash = $fileHash;

        return $this;
    }

    public function getFavorites(): ?int
    {
        return $this->favorites;
    }

    public function setFavorites(int $favorites): static
    {
        $this->favorites = $favorites;

        return $this;
    }

    /**
     * Get file URL using StorageService
     */
    public function getFileUrl(StorageService $storage): string
    {
        return $storage->getUrlFromHash(
            $this->fileHash,
            'beatmap',
            'zip'
        );
    }

    public function getCoverUrl(StorageService $storage): string
    {
        $path = sprintf('beatmaps/%d/covers/list.jpg', $this->id);
        return $storage->getPublicUrl($path);
    }

    public function hasCover(StorageService $storage): bool
    {
        $path = sprintf('beatmaps/%d/covers/list.jpg', $this->id);
        return $storage->$storage->fileExists($path);
    }

    public function getBannerUrl(StorageService $storage): string
    {
        $path = sprintf('beatmaps/%d/covers/banner.png', $this->id);
        return $storage->getPublicUrl($path);
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getApprovedAt(): ?\DateTimeImmutable
    {
        return $this->approvedAt;
    }

    public function setApprovedAt(?\DateTimeImmutable $approvedAt): static
    {
        $this->approvedAt = $approvedAt;

        return $this;
    }

    public function getDownloads(): ?int
    {
        return $this->downloads;
    }

    public function setDownloads(int $downloads): static
    {
        $this->downloads = $downloads;

        return $this;
    }

    public function getFilesize(): ?string
    {
        return $this->filesize;
    }

    public function setFilesize(string $filesize): static
    {
        $this->filesize = $filesize;

        return $this;
    }

    /**
     * @return Collection<int, BeatmapsetComment>
     */
    public function getBeatmapsetComments(): Collection
    {
        return $this->beatmapsetComments;
    }

    public function addBeatmapsetComment(BeatmapsetComment $beatmapsetComment): static
    {
        if (!$this->beatmapsetComments->contains($beatmapsetComment)) {
            $this->beatmapsetComments->add($beatmapsetComment);
            $beatmapsetComment->setBeatmapset($this);
        }

        return $this;
    }

    public function removeBeatmapsetComment(BeatmapsetComment $beatmapsetComment): static
    {
        if ($this->beatmapsetComments->removeElement($beatmapsetComment)) {
            // set the owning side to null (unless already changed)
            if ($beatmapsetComment->getBeatmapset() === $this) {
                $beatmapsetComment->setBeatmapset(null);
            }
        }

        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function isFeatured(): ?bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): static
    {
        $this->featured = $featured;

        return $this;
    }
}
