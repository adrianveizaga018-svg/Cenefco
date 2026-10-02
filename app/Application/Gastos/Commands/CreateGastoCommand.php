<?php

namespace App\Application\Gastos\Commands;

final readonly class CreateGastoCommand
{
    public function __construct(
        public int     $categoria_gasto_id,
        public string  $concepto,
        public float   $monto,
        public string  $fecha,
        public ?string $responsable         = null,
        public ?string $comprobante_url     = null,
        public ?string $nota                = null,
        public ?int    $gasto_recurrente_id = null,
        public ?int    $campana_publicidad_id = null,
    ) {}
}
