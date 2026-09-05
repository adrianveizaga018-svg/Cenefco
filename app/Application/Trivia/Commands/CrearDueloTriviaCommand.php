<?php

namespace App\Application\Trivia\Commands;

final readonly class CrearDueloTriviaCommand
{
    public function __construct(
        public int $categoriaId,
        public int $usuarioId,
    ) {}
}
