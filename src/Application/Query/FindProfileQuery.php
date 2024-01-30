<?php

namespace App\Application\Query;

class FindProfileQuery
{
    public function __construct(public readonly string $id)
    {
    }
}
