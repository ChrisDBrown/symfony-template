<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Model\Entity\Profile;
use App\Domain\Repository\ProfileRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineProfileRepository implements ProfileRepositoryInterface
{
    /** @var EntityRepository<Profile> */
    private readonly EntityRepository $repository;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->repository = $this->entityManager->getRepository(Profile::class);
    }

    #[\Override]
    public function findOneById(string $id): ?Profile
    {
        return $this->repository->find($id);
    }

    #[\Override]
    public function getAll(): array
    {
        return $this->repository->findAll();
    }

    #[\Override]
    public function save(Profile $profile): void
    {
        $this->entityManager->persist($profile);
    }

    #[\Override]
    public function delete(Profile $profile): void
    {
        $this->entityManager->remove($profile);
    }

    #[\Override]
    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
