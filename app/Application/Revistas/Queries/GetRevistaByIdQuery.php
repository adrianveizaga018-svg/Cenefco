<?php

namespace App\Application\Revistas\Queries;

final readonly class GetRevistaByIdQuery
{
    public function __construct(public int $id) {}
}
