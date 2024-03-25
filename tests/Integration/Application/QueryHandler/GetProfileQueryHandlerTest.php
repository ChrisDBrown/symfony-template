<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\QueryHandler;

use App\Application\Exception\NotFoundException;
use App\Application\Query\GetProfileQuery;
use App\Application\QueryHandler\GetProfileQueryHandler;
use App\Tests\Integration\IntegrationTest;
use Symfony\Component\Uid\Uuid;

class GetProfileQueryHandlerTest extends IntegrationTest
{
    private GetProfileQueryHandler $handler;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = static::getContainer()->get(GetProfileQueryHandler::class);
    }

    public function testCanFindExistingProfile(): void
    {
        $existingProfile = $this->persistProfileForName('Chris');

        $result = $this->handler->handle(new GetProfileQuery((string) $existingProfile->getId()));

        self::assertSame($existingProfile, $result);
    }

    public function testThrowOnProfileNotFound(): void
    {
        self::expectException(NotFoundException::class);
        $this->handler->handle(new GetProfileQuery((string) Uuid::v4()));
    }
}
