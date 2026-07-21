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
     * @var Collection<int, UserSession>
     */
    #[ORM\OneToMany(targetEntity: UserSession::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $userSessions;

    /**
     * @var Collection<int, Group>
     */
    #[ORM\ManyToMany(targetEntity: Group::class, inversedBy: 'users')]
    private Collection $groups;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastSeenAt = null;

    /**
     * @var Collection<int, FavoriteBeatmapset>
     */
    #[ORM\OneToMany(targetEntity: FavoriteBeatmapset::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $favoriteBeatmapsets;

    public function __construct()
    {
        $this->beatmapsets = new ArrayCollection();
        $this->beatmapsetComments = new ArrayCollection();
        $this->userSessions = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->favoriteBeatmapsets = new ArrayCollection();
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
     * @return Collection<int, UserSession>
     */
    public function getUserSessions(): Collection
    {
        return $this->userSessions;
    }

    public function addUserSession(UserSession $userSession): static
    {
        if (!$this->userSessions->contains($userSession)) {
            $this->userSessions->add($userSession);
            $userSession->setUser($this);
        }

        return $this;
    }

    public function removeUserSession(UserSession $userSession): static
    {
        if ($this->userSessions->removeElement($userSession)) {
            // set the owning side to null (unless already changed)
            if ($userSession->getUser() === $this) {
                $userSession->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): static
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
        }

        return $this;
    }

    public function removeGroup(Group $group): static
    {
        $this->groups->removeElement($group);

        return $this;
    }

    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];
        foreach ($this->groups as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }
        return array_unique($roles);
    }

    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->getRoles(), true);
    }

    public function getLastSeenAt(): ?\DateTimeImmutable
    {
        return $this->lastSeenAt;
    }

    public function setLastSeenAt(?\DateTimeImmutable $lastSeenAt): static
    {
        $this->lastSeenAt = $lastSeenAt;

        return $this;
    }

    public function isOnline(): bool
    {
        if (!$this->lastSeenAt) {
            return false;
        }

        return $this->lastSeenAt > new \DateTimeImmutable('-6 minutes');
    }

    /**
     * @return Collection<int, FavoriteBeatmapset>
     */
    public function getFavoriteBeatmapsets(): Collection
    {
        return $this->favoriteBeatmapsets;
    }

    public function addFavoriteBeatmapset(FavoriteBeatmapset $favoriteBeatmapset): static
    {
        if (!$this->favoriteBeatmapsets->contains($favoriteBeatmapset)) {
            $this->favoriteBeatmapsets->add($favoriteBeatmapset);
            $favoriteBeatmapset->setUser($this);
        }

        return $this;
    }

    public function removeFavoriteBeatmapset(FavoriteBeatmapset $favoriteBeatmapset): static
    {
        if ($this->favoriteBeatmapsets->removeElement($favoriteBeatmapset)) {
            // set the owning side to null (unless already changed)
            if ($favoriteBeatmapset->getUser() === $this) {
                $favoriteBeatmapset->setUser(null);
            }
        }

        return $this;
    }
}
