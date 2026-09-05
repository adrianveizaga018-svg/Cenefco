<?php

namespace App\Application\Planillas\QueryHandlers;

use App\Application\Planillas\Queries\GetPlanillaPreviewQuery;
use App\Application\Planillas\Services\PlanillaCalculadorService;

class GetPlanillaPreviewQueryHandler
{
    public function __construct(
        private readonly PlanillaCalculadorService $calculador,
    ) {}

    public function handle(GetPlanillaPreviewQuery $query): array
    {
        return $this->calculador->calcularPreview($query->anio, $query->mes);
    }
}
