<?php

declare(strict_types=1);

namespace App\Tests\Functional\Infrastructure\Console;

use App\Tests\Functional\FunctionalTest;
use Symfony\Component\Console\Tester\CommandTester;

class GetProfileConsoleCommandTest extends FunctionalTest
{
    private CommandTester $commandTester;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->commandTester = $this->getCommandTester('app:get-profile');
    }

    /** @test */
    public function canGetSingleProfile(): void
    {
        $profile = $this->persistProfileForName('Chris');

        $this->commandTester->execute(['id' => $profile->getId()]);
        $this->commandTester->assertCommandIsSuccessful();

        $output = $this->commandTester->getDisplay();
        $this::assertStringContainsString('Chris', $output);
    }

    /** @test */
    public function canGetMultipleProfiles(): void
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
