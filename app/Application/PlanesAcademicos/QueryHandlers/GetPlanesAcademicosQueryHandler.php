<?php

namespace App\Application\PlanesAcademicos\QueryHandlers;

use App\Application\PlanesAcademicos\Queries\GetPlanesAcademicosQuery;
use App\Domain\PlanesAcademicos\Contracts\PlanAcademicoRepositoryInterface;

class GetPlanesAcademicosQueryHandler
{
    public function __construct(
        private readonly PlanAcademicoRepositoryInterface $repository
    ) {}

    public function handle(GetPlanesAcademicosQuery $query): array
    {
        return $this->repository->paginate(
            $query->pagination,
            $query->conInactivos,
            $query->idCatplan,
            $query->idMat,
        );
    }
}
