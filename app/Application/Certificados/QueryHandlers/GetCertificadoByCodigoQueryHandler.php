<?php

namespace App\Application\Certificados\QueryHandlers;

use App\Application\Certificados\DTOs\CertificadoDTO;
use App\Application\Certificados\Queries\GetCertificadoByCodigoQuery;
use App\Domain\Certificados\Contracts\CertificadoRepositoryInterface;

class GetCertificadoByCodigoQueryHandler
{
    public function __construct(
        private readonly CertificadoRepositoryInterface $repository
    ) {}

    public function handle(GetCertificadoByCodigoQuery $query): CertificadoDTO
    {
        return CertificadoDTO::fromRow($this->repository->findByCodigo($query->codigo));
    }
}
