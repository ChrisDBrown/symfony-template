<?php

namespace App\Application\QueryHandler;

use App\Application\Query\FindProfileQuery;
use App\Domain\Model\Entity\Profile;
use App\Domain\Repository\ProfileRepositoryInterface;

class FindProfileQueryHandler
{
    public function __construct(private readonly ProfileRepositoryInterface $profileRepository)
    {
    }

    public function handle(FindProfileQuery $command): ?Profile
    {
        return $this->profileRepository->findOneById($command->id);
    }
}
