<?php

namespace App\Application\Gastos\Commands;

final readonly class ConfirmarGastoRecurrenteCommand
{
    public function __construct(
        public int     $gasto_recurrente_id,
        public string  $fecha,
        public ?string $comprobante_url = null,
    ) {}
}
