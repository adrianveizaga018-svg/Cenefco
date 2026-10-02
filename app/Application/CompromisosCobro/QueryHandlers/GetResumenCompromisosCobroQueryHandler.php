<?php

namespace App\Application\CompromisosCobro\QueryHandlers;

use App\Application\CompromisosCobro\Queries\GetResumenCompromisosCobroQuery;
use App\Domain\CompromisosCobro\Contracts\CompromisoCobroRepositoryInterface;

class GetResumenCompromisosCobroQueryHandler
{
    public function __construct(private readonly CompromisoCobroRepositoryInterface $repository) {}

    public function handle(GetResumenCompromisosCobroQuery $query): array
    {
        return $this->repository->resumen($query->idImpPermitidos);
    }
}
