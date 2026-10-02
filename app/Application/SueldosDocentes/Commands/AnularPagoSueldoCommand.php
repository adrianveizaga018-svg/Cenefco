<?php

namespace App\Application\SueldosDocentes\Commands;

final readonly class AnularPagoSueldoCommand
{
    public function __construct(
        public int $idSueldo,
        public int $pagoId,
    ) {}
}
