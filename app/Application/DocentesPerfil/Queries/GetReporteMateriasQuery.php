<?php

namespace App\Application\DocentesPerfil\Queries;

final readonly class GetReporteMateriasQuery
{
    public function __construct(
        public ?string $gestion = null,
        public ?string $periodo = null,
    ) {}
}
