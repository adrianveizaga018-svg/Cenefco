<?php

namespace App\Application\Trivia\Queries;

final readonly class GetTriviaNivelByIdQuery
{
    public function __construct(public int $id) {}
}
