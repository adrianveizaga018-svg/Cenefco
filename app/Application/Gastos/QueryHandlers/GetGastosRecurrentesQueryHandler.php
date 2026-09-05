<?php

namespace App\Application\Gastos\QueryHandlers;

use App\Application\Gastos\Queries\GetGastosRecurrentesQuery;
use App\Domain\Gastos\Contracts\GastoRecurrenteRepositoryInterface;

class GetGastosRecurrentesQueryHandler
{
    public function __construct(private readonly GastoRecurrenteRepositoryInterface $repository) {}

    public function handle(GetGastosRecurrentesQuery $query): array
    {
        return $this->repository->findAllActivos();
    }
}
