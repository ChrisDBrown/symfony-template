<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controller;

use App\Application\Command\CreateProfileCommand;
use App\Application\Command\DeleteProfileCommand;
use App\Application\Command\UpdateProfileCommand;
use App\Application\Query\GetProfileQuery;
use App\Application\Query\GetProfilesQuery;
use App\Domain\Model\Entity\Profile;
use App\Infrastructure\Http\RequestDto\CreateProfileRequestDto;
use App\Infrastructure\Http\RequestDto\UpdateProfileRequestDto;
use JMS\Serializer\SerializerInterface;
use League\Tactician\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/profiles', name: 'profiles_', format: 'json')]
class ProfilesController extends AbstractController
{
    use ApiTrait;

    public function __construct(private readonly CommandBus $commandBus, private readonly CommandBus $queryBus, private readonly SerializerInterface $serializer)
    {
    }

    #[Route('/', name: 'create', methods: [Request::METHOD_POST])]
    public function createProfile(#[MapRequestPayload(acceptFormat: 'json')] CreateProfileRequestDto $dto): JsonResponse
    {
        /** @var Profile $profile */
        $profile = $this->commandBus->handle(new CreateProfileCommand($dto->name));

        return $this->serializeResponse($profile);
    }

    #[Route('/{uuid}', name: 'read', requirements: ['uuid' => Requirement::UUID], methods: [Request::METHOD_GET])]
    public function getProfile(string $uuid): JsonResponse
    {
        /** @var ?Profile $profile */
        $profile = $this->queryBus->handle(new GetProfileQuery($uuid));

        return $this->serializeResponse($profile);
    }

    #[Route('/{uuid}', name: 'update', requirements: ['uuid' => Requirement::UUID], methods: [Request::METHOD_POST])]
    public function updateProfile(string $uuid, #[MapRequestPayload(acceptFormat: 'json')] UpdateProfileRequestDto $dto): JsonResponse
    {
        /** @var Profile $profile */
        $profile = $this->commandBus->handle(new UpdateProfileCommand($uuid, $dto->name));

        return $this->serializeResponse($profile);
    }

    #[Route('/{uuid}', name: 'delete', requirements: ['uuid' => Requirement::UUID], methods: [Request::METHOD_DELETE])]
    public function deleteProfile(string $uuid): JsonResponse
    {
        $this->commandBus->handle(new DeleteProfileCommand($uuid));

        return $this->serializeResponse(status: Response::HTTP_NO_CONTENT);
    }

    #[Route('/', name: 'list', methods: [Request::METHOD_GET])]
    public function listProfiles(): JsonResponse
    {
        /** @var list<Profile> $profiles */
        $profiles = $this->queryBus->handle(new GetProfilesQuery());

        return $this->serializeResponse($profiles);
    }
}
