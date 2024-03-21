<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\CommandHandler;

use App\Application\Command\DeleteProfileCommand;
use App\Application\CommandHandler\DeleteProfileCommandHandler;
use App\Domain\Model\Entity\Profile;
use App\Tests\Integration\IntegrationTest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;

class DeleteProfileCommandHandlerTest extends IntegrationTest
{
    private DeleteProfileCommandHandler $handler;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = static::getContainer()->get(DeleteProfileCommandHandler::class);
    }

    /** @test */
    public function canDeleteProfile(): void
    {
        $persistedProfile = $this->persistProfileForName('Chris');

        $this->handler->handle(new DeleteProfileCommand((string) $persistedProfile->getId()));

        self::assertNull($this->entityManager->getRepository(Profile::class)->findOneById((string) $persistedProfile->getId()));
    }

    /** @test */
    public function throwOnProfileNotFound(): void
    {
        self::expectException(NotFoundHttpException::class);
        $this->handler->handle(new DeleteProfileCommand((string) Uuid::v4()));
    }
}
