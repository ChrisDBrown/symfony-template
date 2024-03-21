<?php

declare(strict_types=1);

namespace App\Application\Command;

class DeleteProfileCommand
{
    public function __construct(public readonly string $id)
    {
    }
}
