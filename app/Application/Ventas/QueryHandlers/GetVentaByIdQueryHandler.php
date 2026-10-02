<?php

namespace App\Application\Ventas\QueryHandlers;

use App\Application\Ventas\Queries\GetVentaByIdQuery;
use App\Domain\Ventas\Contracts\VentaRepositoryInterface;

class GetVentaByIdQueryHandler
{
    public function __construct(
        private readonly VentaRepositoryInterface $repository,
    ) {}

    public function handle(GetVentaByIdQuery $query): object
    {
        return $this->repository->findById($query->id);
    }
}
