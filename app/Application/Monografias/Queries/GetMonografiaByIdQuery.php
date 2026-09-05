<?php

namespace App\Application\Monografias\Queries;

final readonly class GetMonografiaByIdQuery
{
    public function __construct(public int $id) {}
}
