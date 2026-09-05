<?php

namespace App\Application\MediosPago\QueryHandlers;

use App\Application\MediosPago\Queries\GetMediosPagoQuery;
use App\Domain\MediosPago\Contracts\MedioPagoRepositoryInterface;

class GetMediosPagoQueryHandler
{
    public function __construct(private readonly MedioPagoRepositoryInterface $repository) {}

    public function handle(GetMediosPagoQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->query, $query->soloActivos);
    }
}
