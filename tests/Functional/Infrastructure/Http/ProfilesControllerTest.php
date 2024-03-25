<?php

declare(strict_types=1);

namespace App\Tests\Functional\Infrastructure\Http;

use App\Domain\Model\Entity\Profile;
use App\Tests\Functional\FunctionalTest;
use App\Tests\JsonPathTrait;
use Gertjuhh\SymfonyOpenapiValidator\OpenApiValidator;
use Symfony\Component\Uid\Uuid;

class ProfilesControllerTest extends FunctionalTest
{
    use JsonPathTrait;
    use OpenApiValidator;

    #[\Override]
    protected function assertPostConditions(): void
    {
        self::assertOpenApiSchema('public/openapi.json', $this->client);
    }

    public function testCanCreateProfile(): void
    {
        $this->client->jsonRequest('POST', '/profiles/', ['name' => 'Chris']);

        self::assertResponseIsSuccessful();
        $response = $this->client->getResponse()->getContent();
        self::assertJsonValueEquals($response, '$.data.name', 'Chris');

        $id = self::getSingleJsonValueByPath($response, '$.data.id');

        $profile = $this->entityManager->find(Profile::class, $id);
        self::assertInstanceOf(Profile::class, $profile);
        self::assertEquals('Chris', $profile->getName());
    }

    public function testValidationErrorOnCreateProfile(): void
    {
        $this->client->jsonRequest('POST', '/profiles/', ['name' => '']);

        self::assertResponseStatusCodeSame(422);
        $response = $this->client->getResponse()->getContent();

        self::assertJsonValueEquals($response, '$.title', 'Validation Failed');
        /* @TODO: This'll be tedious for more complex validation errors, need a better way */
        self::assertJsonValueEquals($response, '$.detail', sprintf('name: This value should not be blank.%sname: This value is too short. It should have 1 character or more.', \PHP_EOL));
    }

    public function testCanReadProfile(): void
    {
        $existingProfile = $this->persistProfileForName('Chris');

        $this->client->jsonRequest('GET', sprintf('/profiles/%s', $existingProfile->getId()));

        self::assertResponseIsSuccessful();
        $response = $this->client->getResponse()->getContent();
        self::assertJsonValueEquals($response, '$.data.name', 'Chris');
    }

    public function testReadMissingProfileGives404(): void
    {
        $this->client->jsonRequest('GET', sprintf('/profiles/%s', Uuid::v4()));

        self::assertResponseStatusCodeSame(404);
    }

    public function testCanUpdateProfile(): void
    {
        $existingProfile = $this->persistProfileForName('Chris');

        $this->client->jsonRequest('POST', sprintf('/profiles/%s', $existingProfile->getId()), ['name' => 'Kevin']);

        self::assertResponseIsSuccessful();
        $response = $this->client->getResponse()->getContent();
        self::assertJsonValueEquals($response, '$.data.name', 'Kevin');

        $profile = $this->entityManager->find(Profile::class, $existingProfile->getId());
        self::assertInstanceOf(Profile::class, $profile);
        self::assertEquals('Kevin', $profile->getName());
    }

    public function testUpdateMissingProfileGives404(): void
    {
        $this->client->jsonRequest('POST', sprintf('/profiles/%s', Uuid::v4()), ['name' => 'Kevin']);

        self::assertResponseStatusCodeSame(404);
    }

    public function testValidationErrorOnUpdateProfile(): void
    {
        $existingProfile = $this->persistProfileForName('Chris');

        $this->client->jsonRequest('POST', sprintf('/profiles/%s', $existingProfile->getId()), ['name' => '']);

        self::assertResponseStatusCodeSame(422);
        $response = $this->client->getResponse()->getContent();

        self::assertJsonValueEquals($response, '$.title', 'Validation Failed');
        /* @TODO: This'll be tedious for more complex validation errors, need a better way */
        self::assertJsonValueEquals($response, '$.detail', sprintf('name: This value should not be blank.%sname: This value is too short. It should have 1 character or more.', \PHP_EOL));

        $profile = $this->entityManager->find(Profile::class, $existingProfile->getId());
        self::assertInstanceOf(Profile::class, $profile);
        self::assertEquals('Chris', $profile->getName());
    }

    public function testCanDeleteProfile(): void
    {
        $existingProfile = $this->persistProfileForName('Chris');

        $this->client->jsonRequest('DELETE', sprintf('/profiles/%s', $existingProfile->getId()));

        self::assertResponseIsSuccessful();

        $profile = $this->entityManager->find(Profile::class, $existingProfile->getId());
        self::assertNull($profile);
    }

    public function testDeleteMissingProfileGives404(): void
    {
        $this->client->jsonRequest('DELETE', sprintf('/profiles/%s', Uuid::v4()));

        self::assertResponseStatusCodeSame(404);
    }

    public function testCanListProfiles(): void
    {
        $this->persistProfileForName('Chris');
        $this->persistProfileForName('Kevin');

        $this->client->jsonRequest('GET', '/profiles/');

        self::assertResponseIsSuccessful();
        $response = $this->client->getResponse()->getContent();
        self::assertJsonValueEquals($response, '$.data[0].name', 'Chris');
        self::assertJsonValueEquals($response, '$.data[1].name', 'Kevin');
    }
}
