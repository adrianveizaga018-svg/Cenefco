<?php

namespace App\Application\ConfiguracionesAcademicas\QueryHandlers;

use App\Application\ConfiguracionesAcademicas\Queries\GetConfiguracionesAcademicasQuery;
use App\Domain\ConfiguracionesAcademicas\Contracts\ConfiguracionAcademicaRepositoryInterface;

class GetConfiguracionesAcademicasQueryHandler
{
    public function __construct(
        private readonly ConfiguracionAcademicaRepositoryInterface $repository,
    ) {}

    public function handle(GetConfiguracionesAcademicasQuery $query): array
    {
        return $this->repository->paginate(
            $query->pagination,
            $query->gestion,
            $query->idPlan,
            $query->conInactivos,
        );
    }
}
