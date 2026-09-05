<?php

namespace App\Application\Trivia\Commands;

final readonly class MarcarTriviaCanjeEntregadoCommand
{
    public function __construct(
        public int $id,
        public int $entregadoPor,
        public ?string $nota,
    ) {}
}
