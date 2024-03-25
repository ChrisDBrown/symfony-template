<?php

declare(strict_types=1);

namespace App\Application\CommandHandler;

use App\Application\Command\UpdateProfileCommand;
use App\Application\Exception\NotFoundException;
use App\Domain\Model\Entity\Profile;
use App\Domain\Repository\ProfileRepositoryInterface;

class UpdateProfileCommandHandler
{
    public function __construct(private readonly ProfileRepositoryInterface $profileRepository)
    {
    }

    public function handle(UpdateProfileCommand $command): Profile
    {
        $profile = $this->profileRepository->findOneById($command->id);

        if (!$profile instanceof Profile) {
            throw new NotFoundException(sprintf('No profile found for id %s', $command->id));
        }

        $profile->setName($command->name);
        $this->profileRepository->save($profile);

        return $profile;
    }
}
