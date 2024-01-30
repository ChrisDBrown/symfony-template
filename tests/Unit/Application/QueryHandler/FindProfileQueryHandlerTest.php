<?php

namespace App\Tests\Unit\Application\QueryHandler;

use App\Application\Query\FindProfileQuery;
use App\Application\QueryHandler\FindProfileQueryHandler;
use App\Domain\Model\Entity\Profile;
use App\Domain\Repository\ProfileRepositoryInterface;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class FindProfileQueryHandlerTest extends TestCase
{
    private ProfileRepositoryInterface&m\MockInterface $repository;

    private FindProfileQueryHandler $handler;

    #[\Override]
    protected function setUp(): void
    {
        $this->repository = m::mock(ProfileRepositoryInterface::class);
        $this->handler = new FindProfileQueryHandler($this->repository);
    }

    /** @test */
    public function canFindExistingProfile(): void
    {
        $id = Uuid::v4();
        $profile = new Profile($id, 'Chris');

        $this->repository->shouldReceive('findOneById')->with((string) $id)->andReturn($profile);

        self::assertEquals($profile, $this->handler->handle(new FindProfileQuery($id)));
    }

    /** @test */
    public function nullOnNonexistingProfile(): void
    {
        $id = Uuid::v4();

        $this->repository->shouldReceive('findOneById')->with((string) $id)->andReturnNull();

        self::assertNull($this->handler->handle(new FindProfileQuery($id)));
    }
}
