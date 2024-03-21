<?php

declare(strict_types=1);

namespace App\Application\Command;

class UpdateProfileCommand
{
    public function __construct(
        public readonly string $id,
        public readonly string $name
    ) {
    }
}
