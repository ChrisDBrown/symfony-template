<?php

declare(strict_types=1);

namespace App\Application\Query;

class GetProfileQuery
{
    public function __construct(public readonly string $id)
    {
    }
}
