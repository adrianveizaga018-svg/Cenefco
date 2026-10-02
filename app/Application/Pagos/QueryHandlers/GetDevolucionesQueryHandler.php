<?php

namespace App\Application\Pagos\QueryHandlers;

use App\Application\Pagos\DTOs\DevolucionDTO;
use App\Application\Pagos\Queries\GetDevolucionesQuery;
use App\Domain\Pagos\Contracts\DevolucionRepositoryInterface;

class GetDevolucionesQueryHandler
{
    public function __construct(
        private readonly DevolucionRepositoryInterface $repository,
    ) {}

    public function handle(GetDevolucionesQuery $query): array
    {
        return array_map(
            fn($m) => DevolucionDTO::fromModel($m),
            $this->repository->findByInscripcion($query->idIns),
        );
    }
}
