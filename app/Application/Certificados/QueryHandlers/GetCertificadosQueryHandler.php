<?php

namespace App\Application\Certificados\QueryHandlers;

use App\Application\Certificados\Queries\GetCertificadosQuery;
use App\Domain\Certificados\Contracts\CertificadoRepositoryInterface;

class GetCertificadosQueryHandler
{
    public function __construct(
        private readonly CertificadoRepositoryInterface $repository
    ) {}

    public function handle(GetCertificadosQuery $query): array
    {
        return $this->repository->paginate(
            $query->pagination,
            $query->usuarioId,
            $query->imparteId,
            $query->estado,
        );
    }
}
