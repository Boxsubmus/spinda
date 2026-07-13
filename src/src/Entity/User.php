<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_STEAMID', fields: ['steamid'])]
#[ORM\UniqueConstraint(name: 'UNIQ_USERNAME', fields: ['username'])]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $steamid = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatarUrl = null;

    /**
     * @var Collection<int, Beatmapset>
     */
    #[ORM\OneToMany(targetEntity: Beatmapset::class, mappedBy: 'author')]
    private Collection $beatmapsets;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?int $mappingPoints = null;

    #[ORM\Column(length: 2)]
    private ?string $countryAcronym = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $aboutMe = null;

    /**
     * @var Collection<int, BeatmapsetComment>
     */
    #[ORM\OneToMany(targetEntity: BeatmapsetComment::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $beatmapsetComments;

    /**
     * @var Collection<int, ApiToken>
     */
    #[ORM\OneToMany(targetEntity: ApiToken::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $apiTokens;

    public function __construct()
    {
        $this->beatmapsets = new ArrayCollection();
        $this->beatmapsetComments = new ArrayCollection();
        $this->apiTokens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSteamid(): ?string
    {
        return $this->steamid;
    }

    public function setSteamid(string $steamid): static
    {
        $this->steamid = $steamid;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->steamid;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl): static
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    /**
     * @return Collection<int, Beatmapset>
     */
    public function getBeatmapsets(): Collection
    {
        return $this->beatmapsets;
    }

    public function addBeatmapset(Beatmapset $beatmapset): static
    {
        if (!$this->beatmapsets->contains($beatmapset)) {
            $this->beatmapsets->add($beatmapset);
            $beatmapset->setAuthor($this);
        }

        return $this;
    }

    public function removeBeatmapset(Beatmapset $beatmapset): static
    {
        if ($this->beatmapsets->removeElement($beatmapset)) {
            // set the owning side to null (unless already changed)
            if ($beatmapset->getAuthor() === $this) {
                $beatmapset->setAuthor(null);
            }
        }

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

    public function getMappingPoints(): ?int
    {
        return $this->mappingPoints;
    }

    public function setMappingPoints(int $mappingPoints): static
    {
        $this->mappingPoints = $mappingPoints;

        return $this;
    }

    public function getCountryAcronym(): ?string
    {
        return $this->countryAcronym;
    }

    public function setCountryAcronym(string $countryAcronym): static
    {
        $this->countryAcronym = $countryAcronym;

        return $this;
    }

    public function getCountryName(): ?string
    {
        return \App\Helpers::country_name_from_acronym($this->countryAcronym);
    }

    public function getCountryFlagUrl(): ?string
    {
        return \App\Helpers::flag_url($this->countryAcronym);
    }

    public function getAboutMe(): ?string
    {
        return $this->aboutMe;
    }

    public function setAboutMe(?string $aboutMe): static
    {
        $this->aboutMe = $aboutMe;

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
            $beatmapsetComment->setAuthor($this);
        }

        return $this;
    }

    public function removeBeatmapsetComment(BeatmapsetComment $beatmapsetComment): static
    {
        if ($this->beatmapsetComments->removeElement($beatmapsetComment)) {
            // set the owning side to null (unless already changed)
            if ($beatmapsetComment->getAuthor() === $this) {
                $beatmapsetComment->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ApiToken>
     */
    public function getApiTokens(): Collection
    {
        return $this->apiTokens;
    }

    public function addApiToken(ApiToken $apiToken): static
    {
        if (!$this->apiTokens->contains($apiToken)) {
            $this->apiTokens->add($apiToken);
            $apiToken->setUser($this);
        }

        return $this;
    }

    public function removeApiToken(ApiToken $apiToken): static
    {
        if ($this->apiTokens->removeElement($apiToken)) {
            // set the owning side to null (unless already changed)
            if ($apiToken->getUser() === $this) {
                $apiToken->setUser(null);
            }
        }

        return $this;
    }
}
