<?php

namespace App\Application\SueldosDocentes\Commands;

final readonly class CreateSueldoDocenteCommand
{
    public function __construct(
        public int     $id_us,
        public string  $concepto,
        public float   $monto_total,
        public ?string $periodo,
        public ?int    $gestion,
        public ?int    $id_imp,
        public ?string $observacion,
        public ?string $archivo_pdf,
        public ?int    $id_programa = null,
    ) {}
}
