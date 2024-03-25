<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\QueryHandler;

use App\Application\Query\GetProfilesQuery;
use App\Application\QueryHandler\GetProfilesQueryHandler;
use App\Tests\Integration\IntegrationTest;

class GetProfilesQueryHandlerTest extends IntegrationTest
{
    private GetProfilesQueryHandler $handler;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = static::getContainer()->get(GetProfilesQueryHandler::class);
    }

    /**
     * @dataProvider testData
     *
     * @param list<string> $names
     */
    public function testCanGetProfiles(array $names): void
    {
        foreach ($names as $name) {
            $this->persistProfileForName($name);
        }

        $result = $this->handler->handle(new GetProfilesQuery());

        self::assertCount(\count($names), $result);
    }

    protected function testData(): \Generator
    {
        yield 'No profiles' => [[]];

        yield '1 profile' => [['Chris']];

        yield '3 profiles' => [['Chris', 'Kevin', 'Sarah']];
    }
}
