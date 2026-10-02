<?php

namespace App\Application\Cursos\Queries;

final readonly class GetReporteEnviosDocumentosQuery
{
    public function __construct(
        public ?string $fechaInicio = null,
        public ?string $fechaFin = null,
        public ?array $idImpPermitidos = null,
    ) {}
}
