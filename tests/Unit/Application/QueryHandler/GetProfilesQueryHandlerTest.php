<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\QueryHandler;

use App\Application\Query\GetProfilesQuery;
use App\Application\QueryHandler\GetProfilesQueryHandler;
use App\Domain\Model\Entity\Profile;
use App\Domain\Repository\ProfileRepositoryInterface;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class GetProfilesQueryHandlerTest extends TestCase
{
    private ProfileRepositoryInterface&m\MockInterface $repository;

    private GetProfilesQueryHandler $handler;

    #[\Override]
    protected function setUp(): void
    {
        $this->repository = m::mock(ProfileRepositoryInterface::class);
        $this->handler = new GetProfilesQueryHandler($this->repository);
    }

    public function testCanHandleCommand(): void
    {
        $profiles = [
            Profile::create('Chris'),
            Profile::create('Kevin'),
        ];

        $this->repository->shouldReceive('getAll')->andReturn($profiles);

        self::assertEquals($this->handler->handle(new GetProfilesQuery()), $profiles);
    }
}
