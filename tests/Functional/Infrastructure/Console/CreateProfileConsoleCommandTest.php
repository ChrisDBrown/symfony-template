<?php

declare(strict_types=1);

namespace App\Tests\Functional\Infrastructure\Console;

use App\Tests\Functional\FunctionalTest;
use League\Tactician\Bundle\Middleware\InvalidCommandException;
use Symfony\Component\Console\Tester\CommandTester;

class CreateProfileConsoleCommandTest extends FunctionalTest
{
    private CommandTester $commandTester;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->commandTester = $this->getCommandTester('app:create-profile');
    }

    public function testCanCreateProfile(): void
    {
        $this->commandTester->execute(['name' => 'Chris']);
        $this->commandTester->assertCommandIsSuccessful();

        $output = $this->commandTester->getDisplay();
        $this::assertStringContainsString('Chris', $output);
    }

    public function testCommandValidationError(): void
    {
        self::expectException(InvalidCommandException::class);
        $this->commandTester->execute(['name' => '']);
    }
}
