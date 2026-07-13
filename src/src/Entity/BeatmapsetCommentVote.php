<?php

namespace App\Entity;

use App\Repository\BeatmapsetCommentVoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BeatmapsetCommentVoteRepository::class)]
#[ORM\UniqueConstraint(name: 'user_comment_unique', columns: ['user_id', 'comment_id'])]
class BeatmapsetCommentVote
{
    public const TYPE_LIKE = 1;
    public const TYPE_DISLIKE = -1;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?BeatmapsetComment $comment = null;

    #[ORM\Column(nullable: true)]
    private ?int $type = null; // 1 = like, -1 = dislike

    public function getId(): ?int
    {
        return $this->id;
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

    public function getComment(): ?BeatmapsetComment
    {
        return $this->comment;
    }

    public function setComment(?BeatmapsetComment $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): static
    {
        $this->type = $type;

        return $this;
    }
}
