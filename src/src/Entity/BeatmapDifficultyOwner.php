<?php

namespace App\Entity;

use App\Repository\BeatmapDifficultyOwnerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BeatmapDifficultyOwnerRepository::class)]
class BeatmapDifficultyOwner
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'beatmapDifficultyOwners')]
    #[ORM\JoinColumn(nullable: false)]
    private ?BeatmapDifficulty $beatmapDifficulty = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBeatmapDifficulty(): ?BeatmapDifficulty
    {
        return $this->beatmapDifficulty;
    }

    public function setBeatmapDifficulty(?BeatmapDifficulty $beatmapDifficulty): static
    {
        $this->beatmapDifficulty = $beatmapDifficulty;

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
}
