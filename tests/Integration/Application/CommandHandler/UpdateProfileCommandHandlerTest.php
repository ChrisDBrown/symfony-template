<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\CommandHandler;

use App\Application\Command\UpdateProfileCommand;
use App\Application\CommandHandler\UpdateProfileCommandHandler;
use App\Domain\Model\Entity\Profile;
use App\Tests\Integration\IntegrationTest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

class UpdateProfileCommandHandlerTest extends IntegrationTest
{
    private UpdateProfileCommandHandler $handler;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = static::getContainer()->get(UpdateProfileCommandHandler::class);
    }

    /** @test */
    public function canUpdateProfile(): void
    {
        $persistedProfile = $this->persistProfileForName('Chris');

        $this->handler->handle(new UpdateProfileCommand((string) $persistedProfile->getId(), 'Kevin'));

        $updatedProfile = $this->entityManager->getRepository(Profile::class)->findOneById($persistedProfile->getId());
        self::assertInstanceOf(Profile::class, $updatedProfile);
        self::assertEquals('Kevin', $updatedProfile->getName());
    }

    /** @test */
    public function throwOnProfileNotFound(): void
    {
        self::expectException(NotFoundHttpException::class);
        $this->handler->handle(new UpdateProfileCommand((string) Uuid::v4(), 'Kevin'));
    }
}
