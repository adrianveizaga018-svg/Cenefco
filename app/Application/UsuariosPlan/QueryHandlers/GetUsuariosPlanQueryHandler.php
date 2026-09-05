<?php

namespace App\Application\UsuariosPlan\QueryHandlers;

use App\Application\UsuariosPlan\Queries\GetUsuariosPlanQuery;
use App\Domain\UsuariosPlan\Contracts\UsuarioPlanRepositoryInterface;

class GetUsuariosPlanQueryHandler
{
    public function __construct(
        private readonly UsuarioPlanRepositoryInterface $repository,
    ) {}

    public function handle(GetUsuariosPlanQuery $query): array
    {
        return $this->repository->paginate(
            $query->pagination,
            $query->idUs,
            $query->idPlan,
            $query->conInactivos,
        );
    }
}
