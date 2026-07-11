<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setSteamid("76561198799361364");
        $user->setUsername("Starpelly");
        $user->setAvatarUrl("https://avatars.fastly.steamstatic.com/d34123aed6aa056e70725c849cdc0968cff8e75b_full.jpg");
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setMappingPoints(0);
        $user->setCountryAcronym('US');

        $manager->persist($user);

        $manager->flush();

        $this->addReference('user_starpelly', $user);
    }
}
