<?php

namespace App\Application\Gastos\Commands;

final readonly class UpdateGastoCommand
{
    public function __construct(
        public int     $id,
        public ?int    $categoria_gasto_id = null,
        public ?string $concepto           = null,
        public ?float  $monto              = null,
        public ?string $fecha              = null,
        public ?string $responsable        = null,
        public ?string $comprobante_url    = null,
        public ?string $nota               = null,
        public ?int    $campana_publicidad_id = null,
    ) {}
}
