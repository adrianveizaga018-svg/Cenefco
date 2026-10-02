<?php

namespace App\Application\Gastos\QueryHandlers;

use App\Application\Gastos\Queries\GetGastoByIdQuery;
use App\Domain\Gastos\Contracts\GastoRepositoryInterface;

class GetGastoByIdQueryHandler
{
    public function __construct(private readonly GastoRepositoryInterface $repository) {}

    public function handle(GetGastoByIdQuery $query): mixed
    {
        return $this->repository->findById($query->id);
    }
}
