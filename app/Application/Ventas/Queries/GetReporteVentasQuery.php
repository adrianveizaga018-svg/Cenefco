<?php

namespace App\Application\Ventas\Queries;

final readonly class GetReporteVentasQuery
{
    public function __construct(
        public ?string $query   = null,
        public ?string $periodo = null,
        public ?string $gestion = null,
    ) {}
}
