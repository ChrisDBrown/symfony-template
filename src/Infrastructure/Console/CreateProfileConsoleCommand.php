<?php

declare(strict_types=1);

namespace App\Infrastructure\Console;

use App\Application\Command\CreateProfileCommand;
use App\Domain\Model\Entity\Profile;
use League\Tactician\CommandBus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:create-profile')]
class CreateProfileConsoleCommand extends ConsoleCommand
{
    public function __construct(readonly CommandBus $commandBus)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->setDescription('Create a new profile')
            ->addArgument('name', InputArgument::REQUIRED, 'Your name');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        if (!\is_string($name)) {
            $output->write('No name given');

            return 1;
        }

        /** @var Profile $profile */
        $profile = $this->commandBus->handle(new CreateProfileCommand($name));

        $output->write(sprintf('Profile for %s created with id %s', $profile->getName(), $profile->getId()));

        return 0;
    }
}
