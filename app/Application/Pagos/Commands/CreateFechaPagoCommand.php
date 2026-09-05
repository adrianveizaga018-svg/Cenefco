<?php

namespace App\Application\Pagos\Commands;

final readonly class CreateFechaPagoCommand
{
    public function __construct(
        public int     $idPlan,
        public ?string $nroPago,
        public float   $montoAPagar,
        public ?string $fechaInicio,
        public ?string $fechaFin,
        public int     $obligatorio   = 1,
        public ?string $tipoTramite   = null,
        public int     $idUsReg       = 0,
    ) {}
}
