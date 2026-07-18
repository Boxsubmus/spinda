<?php

namespace App\DataFixtures;

use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GroupFixtures extends Fixture
{
    public const ADMIN_GROUP_REFERENCE = 'group-admin';
    public const PLAYTESTER_GROUP_REFERENCE = 'group-playtester';

    public function load(ObjectManager $manager): void
    {
        $admin = new Group();
        $admin->setName('admin');
        $admin->setDisplayName('Admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setColor('76AEBC');
        $manager->persist($admin);
        $this->addReference(self::ADMIN_GROUP_REFERENCE, $admin);

        $playtester = new Group();
        $playtester->setName('playtester');
        $playtester->setDisplayName('Playtester');
        $playtester->setRoles(['ROLE_PLAYTESTER']);
        $playtester->setColor('76AEBC');

        $manager->persist($playtester);
        $this->addReference(self::PLAYTESTER_GROUP_REFERENCE, $playtester);

        $manager->flush();
    }
}
