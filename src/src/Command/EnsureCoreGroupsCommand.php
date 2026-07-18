<?php

namespace App\Command;

use App\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:ensure-core-groups',
    description: 'Add a short description for your command',
)]
class EnsureCoreGroupsCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repo = $this->em->getRepository(Group::class);

        $required = [
            'admin' => ['ROLE_ADMIN'],
            'playtester' => ['ROLE_PLAYTESTER'],
        ];

        foreach ($required as $name => $roles) {
            $group = $repo->findOneBy(['name' => $name]);
            if (!$group) {
                $group = new Group();
                $group->setName($name);
                $output->writeln("Created group: $name");
            }
            $group->setRoles($roles);
            $this->em->persist($group);
        }

        $this->em->flush();
        return Command::SUCCESS;
    }
}
