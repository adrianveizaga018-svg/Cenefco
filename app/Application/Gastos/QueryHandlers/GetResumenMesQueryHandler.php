<?php

namespace App\Application\Gastos\QueryHandlers;

use App\Application\Gastos\Queries\GetResumenMesQuery;
use App\Domain\Gastos\Contracts\GastoRepositoryInterface;

class GetResumenMesQueryHandler
{
    public function __construct(private readonly GastoRepositoryInterface $repository) {}

    public function handle(GetResumenMesQuery $query): array
    {
        return [
            'resumen'           => $this->repository->resumenMes($query->anio, $query->mes),
            'por_linea_negocio' => $this->repository->resumenPorLineaNegocio($query->anio, $query->mes),
        ];
    }
}
