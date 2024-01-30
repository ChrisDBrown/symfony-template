<?php

namespace App\Application\Command;

class CreateProfileCommand
{
    public function __construct(public readonly string $name)
    {
    }
}
