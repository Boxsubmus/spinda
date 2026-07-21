<?php

namespace App\Entity;

use App\Repository\FavoriteBeatmapsetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavoriteBeatmapsetRepository::class)]
class FavoriteBeatmapset
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'favoriteBeatmapsets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Beatmapset $beatmapset = null;

    #[ORM\ManyToOne(inversedBy: 'favoriteBeatmapsets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBeatmapset(): ?Beatmapset
    {
        return $this->beatmapset;
    }

    public function setBeatmapset(?Beatmapset $beatmapset): static
    {
        $this->beatmapset = $beatmapset;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
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
}
