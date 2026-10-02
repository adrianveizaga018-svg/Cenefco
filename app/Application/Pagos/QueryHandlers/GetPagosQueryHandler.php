<?php

namespace App\Application\Pagos\QueryHandlers;

use App\Application\Pagos\Queries\GetPagosQuery;
use App\Domain\Pagos\Contracts\PagoRepositoryInterface;

class GetPagosQueryHandler
{
    public function __construct(
        private readonly PagoRepositoryInterface $repository,
    ) {}

    public function handle(GetPagosQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->filters);
    }
}
