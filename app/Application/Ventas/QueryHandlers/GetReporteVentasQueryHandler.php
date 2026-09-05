<?php

namespace App\Application\Ventas\QueryHandlers;

use App\Application\Ventas\Queries\GetReporteVentasQuery;
use App\Domain\Ventas\Contracts\VentaRepositoryInterface;

class GetReporteVentasQueryHandler
{
    public function __construct(
        private readonly VentaRepositoryInterface $repository,
    ) {}

    public function handle(GetReporteVentasQuery $query): array
    {
        return $this->repository->reporte([
            'query'   => $query->query,
            'periodo' => $query->periodo,
            'gestion' => $query->gestion,
        ]);
    }
}
