<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controller;

use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Exception\LogicException;

trait ApiTrait
{
    private readonly SerializerInterface $serializer;

    /**
     * @param array<string, string> $headers
     */
    public function serializeResponse(mixed $data = null, int $status = 200, array $headers = []): JsonResponse
    {
        if (!isset($this->serializer)) {
            throw new LogicException(sprintf('You must provide a "%s" instance in the "%s::serializer" property, but that property has not been initialized yet.', SerializerInterface::class, static::class));
        }

        if (null === $data) {
            return new JsonResponse($data, $status, $headers);
        }

        return new JsonResponse($this->serializer->serialize(['data' => $data], 'json'), $status, $headers, true);
    }
}
