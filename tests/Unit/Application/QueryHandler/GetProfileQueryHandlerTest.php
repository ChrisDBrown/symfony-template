<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\QueryHandler;

use App\Application\Exception\NotFoundException;
use App\Application\Query\GetProfileQuery;
use App\Application\QueryHandler\GetProfileQueryHandler;
use App\Domain\Model\Entity\Profile;
use App\Domain\Repository\ProfileRepositoryInterface;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class GetProfileQueryHandlerTest extends TestCase
{
    private ProfileRepositoryInterface&m\MockInterface $repository;

    private GetProfileQueryHandler $handler;

    #[\Override]
    protected function setUp(): void
    {
        $this->repository = m::mock(ProfileRepositoryInterface::class);
        $this->handler = new GetProfileQueryHandler($this->repository);
    }

    public function testCanFindExistingProfile(): void
    {
        $id = Uuid::v4();
        $profile = new Profile($id, 'Chris');

        $this->repository->shouldReceive('findOneById')->with((string) $id)->andReturn($profile);

        self::assertEquals($profile, $this->handler->handle(new GetProfileQuery((string) $id)));
    }

    public function testThrowOnProfileNotFound(): void
    {
        $id = Uuid::v4();

        $this->repository->shouldReceive('findOneById')->with((string) $id)->andReturnNull();

        self::expectException(NotFoundException::class);
        $this->handler->handle(new GetProfileQuery((string) $id));
    }
}
