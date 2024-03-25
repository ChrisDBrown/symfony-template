<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Domain\Model\Entity\Profile;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

abstract class IntegrationTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();
    }

    #[\Override]
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    protected function persistProfileForName(string $name): Profile
    {
        $uuid = Uuid::v4();
        $profile = new Profile($uuid, $name);

        $this->entityManager->persist($profile);
        $this->entityManager->flush();

        return $profile;
    }
}
