<?php

declare(strict_types=1);

namespace App\Application\CommandHandler;

use App\Application\Command\DeleteProfileCommand;
use App\Application\Exception\NotFoundException;
use App\Domain\Model\Entity\Profile;
use App\Domain\Repository\ProfileRepositoryInterface;

class DeleteProfileCommandHandler
{
    public function __construct(private readonly ProfileRepositoryInterface $profileRepository)
    {
    }

    public function handle(DeleteProfileCommand $command): void
    {
        $profile = $this->profileRepository->findOneById($command->id);

        if (!$profile instanceof Profile) {
            throw new NotFoundException(sprintf('No profile found for id %s', $command->id));
        }

        $this->profileRepository->delete($profile);
    }
}
