<?php

namespace App\Application\Permisos\QueryHandlers;

use App\Application\Permisos\Queries\GetPermisosQuery;
use App\Domain\Permisos\Contracts\PermisoRepositoryInterface;

class GetPermisosQueryHandler
{
    public function __construct(
        private readonly PermisoRepositoryInterface $repository
    ) {}

    public function handle(GetPermisosQuery $query): array
    {
        return $this->repository->paginate($query->pagination);
    }
}
