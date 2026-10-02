<?php

namespace App\Application\PlanesAcademicos\QueryHandlers;

use App\Application\PlanesAcademicos\DTOs\PlanAcademicoDTO;
use App\Application\PlanesAcademicos\Queries\GetPlanAcademicoByIdQuery;
use App\Domain\PlanesAcademicos\Contracts\PlanAcademicoRepositoryInterface;

class GetPlanAcademicoByIdQueryHandler
{
    public function __construct(
        private readonly PlanAcademicoRepositoryInterface $repository
    ) {}

    public function handle(GetPlanAcademicoByIdQuery $query): PlanAcademicoDTO
    {
        return $this->repository->findById($query->id);
    }
}
