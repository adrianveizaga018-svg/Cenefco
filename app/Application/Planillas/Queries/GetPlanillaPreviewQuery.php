<?php

namespace App\Application\Planillas\Queries;

final readonly class GetPlanillaPreviewQuery
{
    public function __construct(
        public int $anio,
        public int $mes,
    ) {}
}
