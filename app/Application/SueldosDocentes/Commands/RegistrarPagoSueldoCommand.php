<?php

namespace App\Application\SueldosDocentes\Commands;

final readonly class RegistrarPagoSueldoCommand
{
    public function __construct(
        public int     $idSueldo,
        public float   $montoPagado,
        public string  $fechaPago,
        public ?string $nroComprobante,
        public ?string $observacion,
        public ?string $comprobanteArchivo,
    ) {}
}
