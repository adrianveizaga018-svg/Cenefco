<?php

namespace App\Application\Gastos\Queries;

final readonly class GetGastosPendientesDelMesQuery
{
    public function __construct(
        public int $anio,
        public int $mes,
    ) {}
}
