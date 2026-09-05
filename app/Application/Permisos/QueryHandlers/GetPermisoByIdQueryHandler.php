<?php

namespace App\Application\Permisos\QueryHandlers;

use App\Application\Permisos\DTOs\PermisoDTO;
use App\Application\Permisos\Queries\GetPermisoByIdQuery;
use App\Domain\Permisos\Contracts\PermisoRepositoryInterface;

class GetPermisoByIdQueryHandler
{
    public function __construct(
        private readonly PermisoRepositoryInterface $repository
    ) {}

    public function handle(GetPermisoByIdQuery $query): PermisoDTO
    {
        return $this->repository->findById($query->id);
    }
}
