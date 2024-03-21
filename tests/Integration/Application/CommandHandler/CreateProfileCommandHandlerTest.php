<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\CommandHandler;

use App\Application\Command\CreateProfileCommand;
use App\Application\CommandHandler\CreateProfileCommandHandler;
use App\Domain\Model\Entity\Profile;
use App\Tests\Integration\IntegrationTest;

class CreateProfileCommandHandlerTest extends IntegrationTest
{
    private CreateProfileCommandHandler $handler;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = static::getContainer()->get(CreateProfileCommandHandler::class);
    }

    /** @test */
    public function canSaveProfile(): void
    {
        $result = $this->handler->handle(new CreateProfileCommand('Chris'));

        self::assertEquals('Chris', $result->getName());
        self::assertInstanceOf(Profile::class, $this->entityManager->getRepository(Profile::class)->findOneById($result->getId()));
    }
}
