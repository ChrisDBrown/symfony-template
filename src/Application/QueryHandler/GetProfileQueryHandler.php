<?php

declare(strict_types=1);

namespace App\Application\QueryHandler;

use App\Application\Query\GetProfileQuery;
use App\Domain\Model\Entity\Profile;
use App\Domain\Repository\ProfileRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetProfileQueryHandler
{
    public function __construct(private readonly ProfileRepositoryInterface $profileRepository)
    {
    }

    public function handle(GetProfileQuery $command): Profile
    {
        $profile = $this->profileRepository->findOneById($command->id);

        if (!$profile instanceof Profile) {
            // @TODO: Remove HTTP exceptions from Application layer
            throw new NotFoundHttpException(sprintf('No profile found for id %s', $command->id));
        }

        return $profile;
    }
}
