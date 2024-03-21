<?php

declare(strict_types=1);

namespace App\Data\Fixtures;

use App\Domain\Model\Entity\Profile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $manager->persist(Profile::create('Chris'));

        $manager->flush();
    }
}
