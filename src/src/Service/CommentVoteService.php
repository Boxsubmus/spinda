<?php

namespace App\Service;

use App\Entity\BeatmapsetComment;
use App\Entity\BeatmapsetCommentVote;
use App\Entity\User;
use App\Repository\BeatmapsetCommentVoteRepository;
use Doctrine\ORM\EntityManagerInterface;

class CommentVoteService
{
    public function __construct(
        private EntityManagerInterface $em,
        private BeatmapsetCommentVoteRepository $voteRepository,
    ) {
    }

    /**
     * Casts, switches, or retracts a vote. Returns the new vote state (1, -1, or null if retracted).
     */
    public function vote(User $user, BeatmapsetComment $comment, int $type): ?int
    {
        if (!in_array($type, [BeatmapsetCommentVote::TYPE_LIKE, BeatmapsetCommentVote::TYPE_DISLIKE], true)) {
            throw new \InvalidArgumentException('Invalid vote type');
        }

        $existing = $this->voteRepository->findOneBy([
            'user' => $user,
            'comment' => $comment,
        ]);

        if ($existing) {
            if ($existing->getType() === $type) {
                // Clicking the same vote again retracts it
                $this->adjustCounts($comment, $existing->getType(), -1);
                $this->em->remove($existing);
                $this->em->flush();

                return null;
            }

            // Switching from like to dislike or vice versa
            $this->adjustCounts($comment, $existing->getType(), -1);
            $this->adjustCounts($comment, $type, 1);
            $existing->setType($type);
            $this->em->flush();

            return $type;
        }

        $vote = new BeatmapsetCommentVote();
        $vote->setUser($user);
        $vote->setComment($comment);
        $vote->setType($type);

        $this->adjustCounts($comment, $type, 1);

        $this->em->persist($vote);
        $this->em->flush();

        return $type;
    }

    private function adjustCounts(BeatmapsetComment $comment, int $type, int $delta): void
    {
        if ($type === BeatmapsetCommentVote::TYPE_LIKE) {
            $comment->setLikes($comment->getLikes() + $delta);
        } else {
            $comment->setDislikes($comment->getDislikes() + $delta);
        }
    }
}