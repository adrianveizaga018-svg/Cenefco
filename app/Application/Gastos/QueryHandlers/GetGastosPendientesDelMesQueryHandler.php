<?php

namespace App\Application\Gastos\QueryHandlers;

use App\Application\Gastos\Queries\GetGastosPendientesDelMesQuery;
use App\Domain\Gastos\Contracts\GastoRecurrenteRepositoryInterface;

class GetGastosPendientesDelMesQueryHandler
{
    public function __construct(private readonly GastoRecurrenteRepositoryInterface $repository) {}

    public function handle(GetGastosPendientesDelMesQuery $query): array
    {
        return $this->repository->pendientesDelMes($query->anio, $query->mes);
    }
}
