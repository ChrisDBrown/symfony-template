<?php

declare(strict_types=1);

namespace App\Application\Command;

class CreateProfileCommand
{
    public function __construct(public readonly string $name)
    {
    }
}
