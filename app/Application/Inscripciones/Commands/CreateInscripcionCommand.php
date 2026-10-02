<?php

namespace App\Application\Inscripciones\Commands;

final readonly class CreateInscripcionCommand
{
    public function __construct(
        public ?int $id_us_reg,
        public ?string $fecha_ins,
        public int $id_us,
        public int $id_imp,
        public ?int $id_plan,
        public ?string $observacion_ins,
        public ?string $observacion,
        public ?string $periodo,
        public ?string $gestion,
        public int $estado,
        public ?int    $idVendedor  = null,
        public ?string $canalVenta  = 'admin',
    ) {}
}
