<?php

namespace App\Application\Cursos\Queries;

final readonly class GetCursosEstadisticasQuery
{
    public function __construct(
        public string $periodo,
        public string $fecha,
        public ?string $fechaFin = null,
        public ?array $idImpPermitidos = null,
    ) {}
}
