<?php

namespace App\Application\MediosPago\QueryHandlers;

use App\Application\MediosPago\DTOs\MedioPagoDTO;
use App\Application\MediosPago\Queries\GetMedioPagoByIdQuery;
use App\Domain\MediosPago\Contracts\MedioPagoRepositoryInterface;

class GetMedioPagoByIdQueryHandler
{
    public function __construct(private readonly MedioPagoRepositoryInterface $repository) {}

    public function handle(GetMedioPagoByIdQuery $query): MedioPagoDTO
    {
        return MedioPagoDTO::fromRow($this->repository->findById($query->id));
    }
}
