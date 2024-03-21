<?php

declare(strict_types=1);

namespace App\Tests\Functional\Infrastructure\Http;

use App\Domain\Model\Entity\Profile;
use App\Tests\Functional\FunctionalTest;
use App\Tests\JsonPathTrait;

class ProfilesControllerTest extends FunctionalTest
{
    use JsonPathTrait;

    /** @test */
    public function canCreateProfile(): void
    {
        $this->client->jsonRequest('POST', '/profiles/', ['name' => 'Chris']);

        self::assertResponseIsSuccessful();
        $response = $this->client->getResponse()->getContent();
        self::assertJsonValueEquals($response, '$.name', 'Chris');

        $id = self::getSingleJsonValueByPath($response, '$.id');

        $profile = $this->entityManager->find(Profile::class, $id);
        self::assertInstanceOf(Profile::class, $profile);
        self::assertEquals('Chris', $profile->getName());
    }

    /** @test */
    public function canReadProfile(): void
    {
        $existingProfile = $this->persistProfileForName('Chris');

        $this->client->jsonRequest('GET', sprintf('/profiles/%s', $existingProfile->getId()));

        self::assertResponseIsSuccessful();
        $response = $this->client->getResponse()->getContent();
        self::assertJsonValueEquals($response, '$.name', 'Chris');
    }

    /** @test */
    public function canUpdateProfile(): void
    {
        $existingProfile = $this->persistProfileForName('Chris');

        $this->client->jsonRequest('POST', sprintf('/profiles/%s', $existingProfile->getId()), ['name' => 'Kevin']);

        self::assertResponseIsSuccessful();
        $response = $this->client->getResponse()->getContent();
        self::assertJsonValueEquals($response, '$.name', 'Kevin');

        $profile = $this->entityManager->find(Profile::class, $existingProfile->getId());
        self::assertInstanceOf(Profile::class, $profile);
        self::assertEquals('Kevin', $profile->getName());
    }

    /** @test */
    public function canDeleteProfile(): void
    {
        $existingProfile = $this->persistProfileForName('Chris');

        $this->client->jsonRequest('DELETE', sprintf('/profiles/%s', $existingProfile->getId()));

        self::assertResponseIsSuccessful();

        $profile = $this->entityManager->find(Profile::class, $existingProfile->getId());
        self::assertNull($profile);
    }

    /** @test */
    public function canListProfiles(): void
    {
        $this->persistProfileForName('Chris');
        $this->persistProfileForName('Kevin');

        $this->client->jsonRequest('GET', '/profiles/');

        self::assertResponseIsSuccessful();
        $response = $this->client->getResponse()->getContent();
        self::assertJsonValueEquals($response, '$[0].name', 'Chris');
        self::assertJsonValueEquals($response, '$[1].name', 'Kevin');
    }
}
