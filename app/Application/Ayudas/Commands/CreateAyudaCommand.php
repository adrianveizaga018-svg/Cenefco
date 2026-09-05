<?php

namespace App\Application\Ayudas\Commands;

final readonly class CreateAyudaCommand
{
    public function __construct(
        public int     $id_ayuda,
        public int     $id_us_reg,
        public int     $num_ayuda,
        public int     $id_us,
        public ?string $gestion,
        public ?string $monto_pagado,
        public ?string $nro_recibo,
        public ?string $fecha_recibo,
        public ?string $observacion_pago,
        public ?int    $id_categoriatipoayuda,
        public int     $estado,
    ) {}
}
