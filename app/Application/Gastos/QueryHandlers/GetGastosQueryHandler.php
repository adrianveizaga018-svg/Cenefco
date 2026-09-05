<?php

namespace App\Application\Gastos\QueryHandlers;

use App\Application\Gastos\Queries\GetGastosQuery;
use App\Domain\Gastos\Contracts\GastoRepositoryInterface;

class GetGastosQueryHandler
{
    public function __construct(private readonly GastoRepositoryInterface $repository) {}

    public function handle(GetGastosQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->filtros);
    }
}
