<?php

namespace App\Command;

use App\Repository\ApiTokenRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:api-tokens:prune',
    description: 'Delete expired API tokens',
)]
class ApiTokensPruneCommand extends Command
{
    public function __construct(private ApiTokenRepository $repository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = $this->repository->deleteExpired();
        $output->writeln(sprintf('Deleted %d expired API token(s).', $count));

        return Command::SUCCESS;
    }
}
