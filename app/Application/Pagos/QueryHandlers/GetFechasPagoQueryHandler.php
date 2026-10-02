<?php

namespace App\Application\Pagos\QueryHandlers;

use App\Application\Pagos\Queries\GetFechasPagoQuery;
use App\Domain\Pagos\Contracts\FechaPagoRepositoryInterface;

class GetFechasPagoQueryHandler
{
    public function __construct(
        private readonly FechaPagoRepositoryInterface $repository,
    ) {}

    public function handle(GetFechasPagoQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->filters);
    }
}
