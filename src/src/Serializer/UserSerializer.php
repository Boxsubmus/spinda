<?php

namespace App\Serializer;

use App\Entity\User;

class UserSerializer
{
    public static function serializeVerbose(User $user, ?int $rankMapping, ?int $rankKudos): array
    {
        $groupsData = [];
        foreach ($user->getGroups() as $group) {
            $groupsData[] = GroupSerializer::serializeVerbose($group);
        }

        return [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'avatarUrl' => $user->getAvatarUrl(),
            'createdAt' => $user->getCreatedAt(),
            'countryName' => $user->getCountryName(),
            'countryAcronym' => $user->getCountryAcronym(),
            'countryFlagUrl' => $user->getCountryFlagUrl(),

            'mappingPoints' => $user->getMappingPoints(),
            'kudos' => $user->getKudos(),

            'aboutMe' => $user->getAboutMe(),

            'roles' => $user->getRoles(),
            'groups' => $groupsData,

            'isAdmin' => $user->isAdmin(),
            'isOnline' => $user->isOnline(),
            'lastSeenAt' => $user->getLastSeenAt(),

            'rank_mapping' => $rankMapping,
            'rank_kudos' => $rankKudos
        ];
    }
    
    public static function serializeBasic(User $user): array
    {
        return [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'avatarUrl' => $user->getAvatarUrl(),
            'createdAt' => $user->getCreatedAt(),
            'countryName' => $user->getCountryName(),
            'countryAcronym' => $user->getCountryAcronym(),
            'countryFlagUrl' => $user->getCountryFlagUrl(),

            'mappingPoints' => $user->getMappingPoints(),

            'isAdmin' => $user->isAdmin(),
            'isOnline' => $user->isOnline(),
            'lastSeenAt' => $user->getLastSeenAt()
        ];
    }
}