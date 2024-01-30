<?php

namespace App\Tests\Functional\Infrastructure\Console;

use App\Domain\Model\Entity\Profile;
use App\Tests\BaseKernel;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Uid\Uuid;

class GetProfileConsoleCommandTest extends BaseKernel
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

    private function persistProfileForName(string $name): Profile
    {
        $uuid = Uuid::v4();
        $profile = new Profile($uuid, $name);

        $this->entityManager->persist($profile);
        $this->entityManager->flush();

        return $profile;
    }
}
