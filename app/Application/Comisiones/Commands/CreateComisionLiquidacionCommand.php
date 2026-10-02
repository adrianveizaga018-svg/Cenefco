<?php

namespace App\Application\Comisiones\Commands;

final readonly class CreateComisionLiquidacionCommand
{
    public function __construct(
        public int    $vendedorId,
        public string $fechaDesde,
        public string $fechaHasta,
        public ?string $nota = null,
    ) {}
}
