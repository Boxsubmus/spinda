<?php

namespace App\Serializer;

use App\Entity\Group;

class GroupSerializer
{
    public static function serializeVerbose(Group $user): array
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'roles' => $user->getRoles(),
            'color' => $user->getColor(),
            'displayName' => $user->getDisplayName()
        ];
    }
}