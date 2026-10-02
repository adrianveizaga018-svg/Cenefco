<?php

namespace App\Application\UsuariosPlanDoc\QueryHandlers;

use App\Application\UsuariosPlanDoc\Queries\GetUsuariosPlanDocQuery;
use App\Domain\UsuariosPlanDoc\Contracts\UsuarioPlanDocRepositoryInterface;

class GetUsuariosPlanDocQueryHandler
{
    public function __construct(
        private readonly UsuarioPlanDocRepositoryInterface $repository,
    ) {}

    public function handle(GetUsuariosPlanDocQuery $query): array
    {
        return $this->repository->paginate(
            $query->pagination,
            $query->idUs,
            $query->idPlanDoc,
            $query->conInactivos,
        );
    }
}
