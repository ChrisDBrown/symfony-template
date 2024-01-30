<?php

namespace App\Tests\Functional\Infrastructure\Console;

use App\Tests\BaseKernel;
use Symfony\Component\Console\Tester\CommandTester;

class CreateProfileConsoleCommandTest extends BaseKernel
{
    private CommandTester $commandTester;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->commandTester = $this->getCommandTester('app:create-profile');
    }

    /** @test */
    public function canCreateProfile(): void
    {
        $this->commandTester->execute(['name' => 'Chris']);
        $this->commandTester->assertCommandIsSuccessful();

        $output = $this->commandTester->getDisplay();
        $this::assertStringContainsString('Chris', $output);
    }
}
