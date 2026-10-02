<?php

namespace App\Application\Trivia\Queries;

final readonly class GetTriviaNivelesQuery
{
    public function __construct(
        public int $categoriaId,
        public bool $soloActivos = false,
    ) {}
}
