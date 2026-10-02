<?php

namespace App\Application\Pagos\Commands;

final readonly class RegistrarAnticipoCommand
{
    public function __construct(
        public int     $idIns,
        public int     $idUs,
        public float   $montoPagado,
        public int     $idUsReg,
        public ?string $nroBoleta     = null,
        public ?string $fechaDeposito = null,
        public ?string $observacion   = null,
        public ?int    $idUsCajero    = null,
    ) {}
}
