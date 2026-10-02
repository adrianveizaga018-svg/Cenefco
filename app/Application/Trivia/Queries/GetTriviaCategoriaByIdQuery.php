<?php

namespace App\Application\Trivia\Queries;

final readonly class GetTriviaCategoriaByIdQuery
{
    public function __construct(public int $id) {}
}
