<?php

namespace App\Application\SueldosDocentes\Commands;

final readonly class RegistrarPagoSueldoLoteCommand
{
    public function __construct(
        public int   $idSueldo,
        public array $cuotas,
    ) {}
}
