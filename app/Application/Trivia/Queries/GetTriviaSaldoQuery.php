<?php

namespace App\Application\Trivia\Queries;

final readonly class GetTriviaSaldoQuery
{
    public function __construct(public int $usuarioId) {}
}
