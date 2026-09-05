<?php

namespace App\Application\Trivia\Commands;

final readonly class CanjearTriviaPremioCommand
{
    public function __construct(
        public int $usuarioId,
        public int $premioId,
    ) {}
}
