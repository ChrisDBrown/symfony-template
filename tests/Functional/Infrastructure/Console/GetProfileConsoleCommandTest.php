<?php

declare(strict_types=1);

namespace App\Tests\Functional\Infrastructure\Console;

use App\Application\Exception\NotFoundException;
use App\Tests\Functional\FunctionalTest;
use League\Tactician\Bundle\Middleware\InvalidCommandException;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Uid\Uuid;

class GetProfileConsoleCommandTest extends FunctionalTest
{
    private CommandTester $commandTester;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->commandTester = $this->getCommandTester('app:get-profile');
    }

    public function testCanGetSingleProfile(): void
    {
        $profile = $this->persistProfileForName('Chris');

        $this->commandTester->execute(['id' => $profile->getId()]);
        $this->commandTester->assertCommandIsSuccessful();

        $output = $this->commandTester->getDisplay();
        $this::assertStringContainsString('Chris', $output);
    }

    public function testCommandValidationError(): void
    {
        self::expectException(InvalidCommandException::class);
        $this->commandTester->execute(['id' => '']);
    }

    public function testErrorOnMissingProfile(): void
    {
        self::expectException(NotFoundException::class);
        $this->commandTester->execute(['id' => (string) Uuid::v4()]);
    }

    public function testCanGetMultipleProfiles(): void
    {
        $this->persistProfileForName('Chris');
        $this->persistProfileForName('Kevin');

        $this->commandTester->execute([]);
        $this->commandTester->assertCommandIsSuccessful();

        $output = $this->commandTester->getDisplay();
        $this::assertStringContainsString('Chris', $output);
        $this::assertStringContainsString('Kevin', $output);
    }
}
