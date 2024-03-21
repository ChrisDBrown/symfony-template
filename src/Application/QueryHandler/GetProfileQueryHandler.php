<?php

declare(strict_types=1);

namespace App\Application\QueryHandler;

use App\Application\Exception\NotFoundException;
use App\Application\Query\GetProfileQuery;
use App\Domain\Model\Entity\Profile;
use App\Domain\Repository\ProfileRepositoryInterface;

class GetProfileQueryHandler
{
    public function __construct(private readonly ProfileRepositoryInterface $profileRepository)
    {
    }

    public function handle(GetProfileQuery $command): Profile
    {
        $profile = $this->profileRepository->findOneById($command->id);

        if (!$profile instanceof Profile) {
            throw new NotFoundException(sprintf('No profile found for id %s', $command->id));
        }

        return $profile;
    }
}
