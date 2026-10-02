<?php

namespace App\Application\CampanasPublicidad\Queries;

final readonly class GetReporteCampanasQuery
{
    public function __construct(
        public ?string $fechaInicio = null,
        public ?string $fechaFin = null,
    ) {}
}
