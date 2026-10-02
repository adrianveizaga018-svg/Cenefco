<?php

namespace App\Application\Reportes\Queries;

final readonly class GetVentasPorPeriodoQuery
{
    public function __construct(
        public string  $fechaInicio,
        public string  $fechaFin,
        public ?int    $usuarioId = null,
    ) {}
}
