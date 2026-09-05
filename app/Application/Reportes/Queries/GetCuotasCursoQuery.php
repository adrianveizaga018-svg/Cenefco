<?php

namespace App\Application\Reportes\Queries;

final readonly class GetCuotasCursoQuery
{
    public function __construct(
        public ?int    $idImp,
        public ?string $fechaInicio,
        public ?string $fechaFin,
        public bool    $conInactivos = false,
        public ?array  $idImpPermitidos = null,
    ) {}
}
