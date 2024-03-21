<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\QueryHandler;

use App\Application\Query\GetProfileQuery;
use App\Application\QueryHandler\GetProfileQueryHandler;
use App\Tests\Integration\IntegrationTest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

    /** @test */
    public function canFindExistingProfile(): void
    {
        $existingProfile = $this->persistProfileForName('Chris');

        $result = $this->handler->handle(new GetProfileQuery((string) $existingProfile->getId()));

        self::assertSame($existingProfile, $result);
    }

    /** @test */
    public function throwOnProfileNotFound(): void
    {
        self::expectException(NotFoundHttpException::class);
        $this->handler->handle(new GetProfileQuery((string) Uuid::v4()));
    }
}
