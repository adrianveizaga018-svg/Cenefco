<?php

namespace App\Application\Ayudas\Commands;

final readonly class UpdateAyudaCommand
{
    public function __construct(
        public int     $id,
        public ?string $gestion,
        public ?string $monto_pagado,
        public ?string $nro_recibo,
        public ?string $fecha_recibo,
        public ?string $observacion_pago,
        public ?int    $id_categoriatipoayuda,
        public ?int    $estado,
    ) {}
}
