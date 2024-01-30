<?php

namespace App\Tests;

use App\Kernel;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

abstract class BaseKernel extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();
    }

    #[\Override]
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function getCommandTester(string $name): CommandTester
    {
        /** @var Kernel $kernel */
        $kernel = self::$kernel;
        $app = new Application($kernel);
        $command = $app->find($name);

        return new CommandTester($command);
    }
}
