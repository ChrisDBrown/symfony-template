<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Repository;

use App\Domain\Model\Entity\Profile;
use App\Infrastructure\Repository\DoctrineProfileRepository;
use App\Tests\Integration\IntegrationTest;

class DoctrineProfileRepositoryTest extends IntegrationTest
{
    private DoctrineProfileRepository $repository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->repository = static::getContainer()->get(DoctrineProfileRepository::class);
    }

    /** @test */
    public function canSaveProfile(): void
    {
        $profile = Profile::create('Chris');
        $this->repository->save($profile);

        $result = $this->entityManager->find(Profile::class, $profile->getId());

        self::assertNotNull($result);
        self::assertEquals($profile->getId(), $result->getId());
    }

    /** @test */
    public function canGetAllProfiles(): void
    {
        $this->persistProfileForName('Chris');
        $this->persistProfileForName('Kevin');

        $result = $this->repository->getAll();

        self::assertCount(2, $result);
        self::assertEquals('Chris', $result[0]->getName());
        self::assertEquals('Kevin', $result[1]->getName());
    }

    /** @test */
    public function canDeleteProfile(): void
    {
        $this->persistProfileForName('Chris');
        $this->persistProfileForName('Kevin');

        $beforeDelete = $this->repository->getAll();
        self::assertCount(2, $beforeDelete);

        $this->repository->delete($beforeDelete[0]);

        $afterDelete = $this->repository->getAll();
        self::assertCount(1, $afterDelete);
        self::assertEquals('Kevin', $afterDelete[0]->getName());
    }
}
