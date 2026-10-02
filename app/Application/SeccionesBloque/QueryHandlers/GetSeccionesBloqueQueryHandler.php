<?php

namespace App\Application\SeccionesBloque\QueryHandlers;

use App\Application\SeccionesBloque\Queries\GetSeccionesBloqueQuery;
use App\Domain\SeccionesBloque\Contracts\SeccionBloqueRepositoryInterface;

class GetSeccionesBloqueQueryHandler
{
    public function __construct(
        private readonly SeccionBloqueRepositoryInterface $repository,
    ) {}

    public function handle(GetSeccionesBloqueQuery $query): array
    {
        return $this->repository->paginate(
            $query->pagination,
            $query->idBloqueajustable,
            $query->conInactivos,
        );
    }
}
