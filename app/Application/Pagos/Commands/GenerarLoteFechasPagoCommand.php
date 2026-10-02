<?php

namespace App\Application\Pagos\Commands;

final readonly class GenerarLoteFechasPagoCommand
{
    public function __construct(
        public int     $idPlan,
        public int     $nroCuotas,
        public float   $montoTotal,
        public string  $fechaInicio,
        public string  $intervalo,
        public string  $tipoTramite,
        public int     $idUsReg = 0,
    ) {}
}
