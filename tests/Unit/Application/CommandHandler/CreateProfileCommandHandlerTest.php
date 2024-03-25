<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\CommandHandler;

use App\Application\Command\CreateProfileCommand;
use App\Application\CommandHandler\CreateProfileCommandHandler;
use App\Domain\Repository\ProfileRepositoryInterface;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class CreateProfileCommandHandlerTest extends TestCase
{
    private ProfileRepositoryInterface&m\MockInterface $repository;

    private CreateProfileCommandHandler $handler;

    #[\Override]
    protected function setUp(): void
    {
        $this->repository = m::mock(ProfileRepositoryInterface::class);
        $this->handler = new CreateProfileCommandHandler($this->repository);
    }

    public function testCanSaveProfile(): void
    {
        $this->repository->shouldReceive('save')->once();

        $result = $this->handler->handle(new CreateProfileCommand('Chris'));

        self::assertEquals('Chris', $result->getName());
    }
}
