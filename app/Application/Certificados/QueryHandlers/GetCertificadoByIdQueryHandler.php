<?php

namespace App\Application\Certificados\QueryHandlers;

use App\Application\Certificados\DTOs\CertificadoDTO;
use App\Application\Certificados\Queries\GetCertificadoByIdQuery;
use App\Domain\Certificados\Contracts\CertificadoRepositoryInterface;

class GetCertificadoByIdQueryHandler
{
    public function __construct(
        private readonly CertificadoRepositoryInterface $repository
    ) {}

    public function handle(GetCertificadoByIdQuery $query): CertificadoDTO
    {
        return CertificadoDTO::fromRow($this->repository->findById($query->id));
    }
}
