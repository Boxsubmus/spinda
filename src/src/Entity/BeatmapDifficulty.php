<?php

namespace App\Entity;

use App\Repository\BeatmapDifficultyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BeatmapDifficultyRepository::class)]
class BeatmapDifficulty
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'beatmapDifficulties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Beatmapset $beatmapset = null;

    #[ORM\Column]
    private ?int $countTap = null;

    #[ORM\Column]
    private ?int $countHold = null;

    #[ORM\Column]
    private ?int $totalLength = null;

    #[ORM\Column]
    private ?float $bpm = null;

    /**
     * @var Collection<int, BeatmapDifficultyOwner>
     */
    #[ORM\OneToMany(targetEntity: BeatmapDifficultyOwner::class, mappedBy: 'beatmapDifficulty', orphanRemoval: true)]
    private Collection $beatmapDifficultyOwners;

    #[ORM\Column(length: 64)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $difficulty = null;

    public function __construct()
    {
        $this->beatmapOwners = new ArrayCollection();
        $this->beatmapDifficultyOwners = new ArrayCollection();
    }

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

    public function getCountTap(): ?int
    {
        return $this->countTap;
    }

    public function setCountTap(int $countTap): static
    {
        $this->countTap = $countTap;

        return $this;
    }

    public function getCountHold(): ?int
    {
        return $this->countHold;
    }

    public function setCountHold(int $countHold): static
    {
        $this->countHold = $countHold;

        return $this;
    }

    public function getTotalLength(): ?int
    {
        return $this->totalLength;
    }

    public function setTotalLength(int $totalLength): static
    {
        $this->totalLength = $totalLength;

        return $this;
    }

    public function getBpm(): ?float
    {
        return $this->bpm;
    }

    public function setBpm(float $bpm): static
    {
        $this->bpm = $bpm;

        return $this;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, BeatmapDifficultyOwner>
     */
    public function getBeatmapDifficultyOwners(): Collection
    {
        return $this->beatmapDifficultyOwners;
    }

    public function addBeatmapDifficultyOwner(BeatmapDifficultyOwner $beatmapDifficultyOwner): static
    {
        if (!$this->beatmapDifficultyOwners->contains($beatmapDifficultyOwner)) {
            $this->beatmapDifficultyOwners->add($beatmapDifficultyOwner);
            $beatmapDifficultyOwner->setBeatmapDifficulty($this);
        }

        return $this;
    }

    public function removeBeatmapDifficultyOwner(BeatmapDifficultyOwner $beatmapDifficultyOwner): static
    {
        if ($this->beatmapDifficultyOwners->removeElement($beatmapDifficultyOwner)) {
            // set the owning side to null (unless already changed)
            if ($beatmapDifficultyOwner->getBeatmapDifficulty() === $this) {
                $beatmapDifficultyOwner->setBeatmapDifficulty(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDifficulty(): ?int
    {
        return $this->difficulty;
    }

    public function setDifficulty(int $difficulty): static
    {
        $this->difficulty = $difficulty;

        return $this;
    }
}
