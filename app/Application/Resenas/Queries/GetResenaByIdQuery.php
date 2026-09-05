<?php

namespace App\Application\Resenas\Queries;

final readonly class GetResenaByIdQuery
{
    public function __construct(public int $id) {}
}
