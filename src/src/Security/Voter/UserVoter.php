<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserVoter extends Voter
{
    public const VIEW = 'USER_VIEW';
    public const EDIT = 'USER_EDIT';

    private const SUPPORTED_ATTRIBUTES = [
        self::VIEW,
        self::EDIT
    ];

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, self::SUPPORTED_ATTRIBUTES, true)
            && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        /** @var \App\Entity\User|null $authUser */
        $authUser = $token->getUser();

        if (!$authUser instanceof User) {
            $vote?->addReason('The user must be logged in to access this resource.');
            return false;
        }

        /** @var User $subject */
        return match ($attribute) {
            self::VIEW => true, // profiles are public
            self::EDIT => $this->canEdit($authUser, $subject, $vote),
        };
    }

    private function canEdit(User $authUser, User $subject, ?Vote $vote): bool
    {
        if ($authUser === $subject || $authUser->isAdmin()) {
            return true;
        }

        $vote?->addReason('You can only edit your own profile.');
        return false;
    }
}
