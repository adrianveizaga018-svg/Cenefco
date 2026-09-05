<?php

namespace App\Application\Trivia\Commands;

final readonly class UnirseDueloTriviaCommand
{
    public function __construct(
        public string $codigoSala,
        public int $usuarioId,
    ) {}
}
