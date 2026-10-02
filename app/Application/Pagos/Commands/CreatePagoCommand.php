<?php

namespace App\Application\Pagos\Commands;

final readonly class CreatePagoCommand
{
    public function __construct(
        public int     $idUs,
        public float   $montoPagado,
        public ?int    $idFechapago    = null,
        public ?int    $idMat          = null,
        public ?int    $idIns          = null,
        public ?string $nroBoleta      = null,
        public ?string $fechaDeposito  = null,
        public ?string $nroNit         = null,
        public ?string $nombreNit      = null,
        public ?int    $tipoFechapago  = null,
        public ?string $observacion    = null,
        public int     $idUsReg        = 0,
        public ?string $metodoPago     = null,
        public ?int    $idUsCajero     = null,
        public ?string $comprobanteArchivo = null,
        public ?float  $montoDescuento = null,
        public ?string $motivoDescuento = null,
        public ?int    $tipoBancoId    = null,
    ) {}
}
