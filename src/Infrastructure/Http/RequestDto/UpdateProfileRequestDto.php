<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\RequestDto;

class UpdateProfileRequestDto
{
    public function __construct(public readonly string $name)
    {
    }
}
