<?php

namespace App\Application\Comisiones\Queries;

final readonly class GetComisionSugeridaQuery
{
    public function __construct(
        public int    $vendedorId,
        public string $fechaDesde,
        public string $fechaHasta,
    ) {}
}
