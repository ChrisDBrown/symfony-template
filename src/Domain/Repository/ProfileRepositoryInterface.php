<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Entity\Profile;

interface ProfileRepositoryInterface
{
    public function findOneById(string $id): ?Profile;

    /** @return list<Profile> */
    public function getAll(): array;

    public function save(Profile $profile): void;

    public function delete(Profile $profile): void;

    public function flush(): void;
}
