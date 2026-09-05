<?php

namespace App\Application\CompromisosCobro\QueryHandlers;

use App\Application\CompromisosCobro\Queries\GetCompromisosCobroQuery;
use App\Domain\CompromisosCobro\Contracts\CompromisoCobroRepositoryInterface;

class GetCompromisosCobroQueryHandler
{
    public function __construct(private readonly CompromisoCobroRepositoryInterface $repository) {}

    public function handle(GetCompromisosCobroQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->estado, $query->idImpPermitidos);
    }
}
