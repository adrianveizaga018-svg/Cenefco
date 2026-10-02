<?php

namespace App\Application\Gastos\QueryHandlers;

use App\Application\Gastos\Queries\GetCategoriasGastoQuery;
use App\Domain\Gastos\Contracts\CategoriaGastoRepositoryInterface;

class GetCategoriasGastoQueryHandler
{
    public function __construct(private readonly CategoriaGastoRepositoryInterface $repository) {}

    public function handle(GetCategoriasGastoQuery $query): array
    {
        return $this->repository->findAllActivas();
    }
}
