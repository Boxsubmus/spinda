<?php

namespace App\Serializer;

use App\Entity\User;

class UserSerializer
{
    public static function serializeVerbose(User $user): array
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

            'aboutMe' => $user->getAboutMe()
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
        ];
    }
}