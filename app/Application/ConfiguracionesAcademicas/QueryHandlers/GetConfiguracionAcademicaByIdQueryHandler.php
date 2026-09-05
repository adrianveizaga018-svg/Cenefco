<?php

namespace App\Application\ConfiguracionesAcademicas\QueryHandlers;

use App\Application\ConfiguracionesAcademicas\DTOs\ConfiguracionAcademicaDTO;
use App\Application\ConfiguracionesAcademicas\Queries\GetConfiguracionAcademicaByIdQuery;
use App\Domain\ConfiguracionesAcademicas\Contracts\ConfiguracionAcademicaRepositoryInterface;

class GetConfiguracionAcademicaByIdQueryHandler
{
    public function __construct(
        private readonly ConfiguracionAcademicaRepositoryInterface $repository,
    ) {}

    public function handle(GetConfiguracionAcademicaByIdQuery $query): ConfiguracionAcademicaDTO
    {
        return ConfiguracionAcademicaDTO::fromRow(
            $this->repository->findById($query->idConf)
        );
    }
}
