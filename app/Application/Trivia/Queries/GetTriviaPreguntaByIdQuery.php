<?php

namespace App\Application\Trivia\Queries;

final readonly class GetTriviaPreguntaByIdQuery
{
    public function __construct(public int $id) {}
}
