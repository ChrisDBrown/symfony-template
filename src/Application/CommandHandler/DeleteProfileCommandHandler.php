<?php

declare(strict_types=1);

namespace App\Application\CommandHandler;

use App\Application\Command\DeleteProfileCommand;
use App\Domain\Model\Entity\Profile;
use App\Domain\Repository\ProfileRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteProfileCommandHandler
{
    public function __construct(private readonly ProfileRepositoryInterface $profileRepository)
    {
    }

    public function handle(DeleteProfileCommand $command): void
    {
        $profile = $this->profileRepository->findOneById($command->id);

        if (!$profile instanceof Profile) {
            // @TODO: Remove HTTP exceptions from Application layer
            throw new NotFoundHttpException(sprintf('No profile found for id %s', $command->id));
        }

        $this->profileRepository->delete($profile);
    }
}
