<?php

namespace App\Application\Trivia\Queries;

final readonly class GetTriviaCategoriaBySlugQuery
{
    public function __construct(public string $slug) {}
}
