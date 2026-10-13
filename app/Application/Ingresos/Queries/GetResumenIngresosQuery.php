<?php

namespace App\Application\Ingresos\Queries;

/** Sin año/mes se resume el mes en curso. */
final readonly class GetResumenIngresosQuery
{
    public function __construct(
        public ?int $anio = null,
        public ?int $mes = null,
    ) {}
}
