<?php

declare(strict_types=1);

namespace App\Infrastructure\Console;

use App\Application\Query\GetProfileQuery;
use App\Application\Query\GetProfilesQuery;
use App\Domain\Model\Entity\Profile;
use League\Tactician\CommandBus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:get-profile')]
class GetProfileConsoleCommand extends ConsoleCommand
{
    public function __construct(readonly CommandBus $queryBus)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->setDescription('Find a profile by ID, or list all')
            ->addArgument('id', InputArgument::OPTIONAL, 'Profile ID');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = $input->getArgument('id');
        if (\is_string($id)) {
            /** @var Profile $profile */
            $profile = $this->queryBus->handle(new GetProfileQuery($id));
            $output->write(sprintf('%s %s', $profile->getId(), $profile->getName()));

            return 0;
        }

        /** @var list<Profile> $profiles */
        $profiles = $this->queryBus->handle(new GetProfilesQuery());
        foreach ($profiles as $profile) {
            $output->writeln(sprintf('%s %s', $profile->getId(), $profile->getName()));
        }

        return 0;
    }
}
