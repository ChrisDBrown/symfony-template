<?php

declare(strict_types=1);

namespace App\Tests;

use Flow\JSONPath\JSONPath;

trait JsonPathTrait
{
    public static function getSingleJsonValueByPath(mixed $data, string $jsonPath): mixed
    {
        if (\is_string($data)) {
            $data = json_decode($data, associative: true, flags: \JSON_THROW_ON_ERROR);
        }

        $result = (new JSONPath($data))->find($jsonPath);

        if (1 !== \count($result)) {
            throw new \RuntimeException(sprintf('%d results found for path %s in object where exactly 1 was expected', \count($result), $jsonPath));
        }

        return $result[0];
    }
}
