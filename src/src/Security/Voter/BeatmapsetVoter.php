<?php

namespace App\Security\Voter;

use App\Entity\Beatmapset;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class BeatmapsetVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        if (!in_array($attribute, [self::EDIT, self::VIEW])) {
            return false;
        }

        // only vote on 'Beatmapset' objects
        if (!$subject instanceof Beatmapset) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        /** @var \App\Entity\User|null $authUser */
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            $vote?->addReason('The user is not logged in.');
            return false;
        }

        /** @var Beatmapset $beatmapset */
        $beatmapset = $subject;

        return match($attribute) {
            self::VIEW => true, // beatmapsets are public
            self::EDIT => $this->canEditPublic($beatmapset, $user, $vote)
        };
    }

    private function canEditPublic(Beatmapset $beatmapset, User $user, ?Vote $vote): bool
    {
        if ($user === $beatmapset->getAuthor() || $user->isAdmin()) {
            return true;
        }

        $vote?->addReason('You can only edit mapsets you own.');
        return false;
    }
}
