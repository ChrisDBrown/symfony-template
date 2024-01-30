<?php

namespace App\Tests\Integration\Infrastructure\Repository;

use App\Domain\Model\Entity\Profile;
use App\Infrastructure\Repository\DoctrineProfileRepository;
use App\Tests\BaseKernel;
use Symfony\Component\Uid\Uuid;

class DoctrineProfileRepositoryTest extends BaseKernel
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

    private function persistProfileForName(string $name): void
    {
        $uuid = Uuid::v4();
        $profile = new Profile($uuid, $name);

        $this->entityManager->persist($profile);
        $this->entityManager->flush();
    }
}
