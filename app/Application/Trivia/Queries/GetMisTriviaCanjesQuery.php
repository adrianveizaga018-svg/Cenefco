<?php

namespace App\Application\Trivia\Queries;

final readonly class GetMisTriviaCanjesQuery
{
    public function __construct(public int $usuarioId) {}
}
