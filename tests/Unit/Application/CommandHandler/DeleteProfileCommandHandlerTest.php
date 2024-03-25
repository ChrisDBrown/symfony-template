<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\CommandHandler;

use App\Application\Command\DeleteProfileCommand;
use App\Application\CommandHandler\DeleteProfileCommandHandler;
use App\Application\Exception\NotFoundException;
use App\Domain\Model\Entity\Profile;
use App\Domain\Repository\ProfileRepositoryInterface;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class DeleteProfileCommandHandlerTest extends TestCase
{
    private ProfileRepositoryInterface&m\MockInterface $repository;

    private DeleteProfileCommandHandler $handler;

    #[\Override]
    protected function setUp(): void
    {
        $this->repository = m::mock(ProfileRepositoryInterface::class);
        $this->handler = new DeleteProfileCommandHandler($this->repository);
    }

    public function testCanDeleteProfile(): void
    {
        $profile = Profile::create('Chris');

        $this->repository->shouldReceive('findOneById')->with((string) $profile->getId())->once()->andReturn($profile);
        $this->repository->shouldReceive('delete')->with($profile)->once();

        $this->handler->handle(new DeleteProfileCommand((string) $profile->getId()));
        self::expectNotToPerformAssertions();
    }

    public function testThrowOnProfileNotFound(): void
    {
        $fakeId = Uuid::v4();

        $this->repository->shouldReceive('findOneById')->with((string) $fakeId)->once()->andReturn(null);

        self::expectException(NotFoundException::class);
        $this->handler->handle(new DeleteProfileCommand((string) $fakeId));
    }
}
