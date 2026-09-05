<?php

namespace App\Application\Dashboard\Queries;

final readonly class GetDashboardGastosQuery
{
    public function __construct(
        public int $anio,
        public int $mes,
    ) {}
}
