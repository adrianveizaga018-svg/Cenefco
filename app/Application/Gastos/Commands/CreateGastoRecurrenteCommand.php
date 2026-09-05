<?php

namespace App\Application\Gastos\Commands;

final readonly class CreateGastoRecurrenteCommand
{
    public function __construct(
        public int    $categoria_gasto_id,
        public string $concepto,
        public float  $monto,
        public int    $dia_del_mes,
        public bool   $activo = true,
    ) {}
}
