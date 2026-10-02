<?php

namespace App\Application\Trivia\DTOs;

final readonly class TriviaSaldoDTO
{
    public function __construct(
        public int $puntaje_total,
        public int $puntos_gastados,
        public int $saldo_disponible,
    ) {}
}
