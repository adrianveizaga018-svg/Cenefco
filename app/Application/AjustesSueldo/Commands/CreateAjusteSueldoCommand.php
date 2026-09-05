<?php

namespace App\Application\AjustesSueldo\Commands;

final readonly class CreateAjusteSueldoCommand
{
    public function __construct(
        public int    $empleadoId,
        public int    $anio,
        public int    $mes,
        public string $tipo,
        public float  $monto,
        public string $motivo,
        public ?int   $registradoPor,
    ) {}
}
