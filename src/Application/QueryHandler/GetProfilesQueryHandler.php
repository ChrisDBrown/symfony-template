<?php

declare(strict_types=1);

namespace App\Application\QueryHandler;

use App\Application\Query\GetProfilesQuery;
use App\Domain\Model\Entity\Profile;
use App\Domain\Repository\ProfileRepositoryInterface;

class GetProfilesQueryHandler
{
    public function __construct(private readonly ProfileRepositoryInterface $profileRepository)
    {
    }

    /** @return list<Profile> */
    public function handle(GetProfilesQuery $command): array
    {
        return $this->profileRepository->getAll();
    }
}
