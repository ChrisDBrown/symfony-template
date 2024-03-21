<?php

declare(strict_types=1);

namespace App\Application\CommandHandler;

use App\Application\Command\CreateProfileCommand;
use App\Domain\Model\Entity\Profile;
use App\Domain\Repository\ProfileRepositoryInterface;

class CreateProfileCommandHandler
{
    public function __construct(private readonly ProfileRepositoryInterface $profileRepository)
    {
    }

    public function handle(CreateProfileCommand $command): Profile
    {
        $profile = Profile::create($command->name);
        $this->profileRepository->save($profile);

        return $profile;
    }
}
