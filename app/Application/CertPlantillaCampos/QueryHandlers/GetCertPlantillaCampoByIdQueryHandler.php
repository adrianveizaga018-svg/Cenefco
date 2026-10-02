<?php

namespace App\Application\CertPlantillaCampos\QueryHandlers;

use App\Application\CertPlantillaCampos\DTOs\CertPlantillaCampoDTO;
use App\Application\CertPlantillaCampos\Queries\GetCertPlantillaCampoByIdQuery;
use App\Domain\CertPlantillaCampos\Contracts\CertPlantillaCampoRepositoryInterface;

class GetCertPlantillaCampoByIdQueryHandler
{
    public function __construct(
        private readonly CertPlantillaCampoRepositoryInterface $repository
    ) {}

    public function handle(GetCertPlantillaCampoByIdQuery $query): CertPlantillaCampoDTO
    {
        return CertPlantillaCampoDTO::fromRow($this->repository->findById($query->id));
    }
}
