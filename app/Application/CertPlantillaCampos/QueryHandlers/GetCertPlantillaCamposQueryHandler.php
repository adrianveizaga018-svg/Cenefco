<?php

namespace App\Application\CertPlantillaCampos\QueryHandlers;

use App\Application\CertPlantillaCampos\Queries\GetCertPlantillaCamposQuery;
use App\Domain\CertPlantillaCampos\Contracts\CertPlantillaCampoRepositoryInterface;

class GetCertPlantillaCamposQueryHandler
{
    public function __construct(
        private readonly CertPlantillaCampoRepositoryInterface $repository
    ) {}

    public function handle(GetCertPlantillaCamposQuery $query): array
    {
        return $this->repository->paginate(
            $query->pagination,
            $query->plantillaId,
            $query->soloActivos,
        );
    }
}
