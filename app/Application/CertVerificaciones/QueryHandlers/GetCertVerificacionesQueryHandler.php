<?php

namespace App\Application\CertVerificaciones\QueryHandlers;

use App\Application\CertVerificaciones\Queries\GetCertVerificacionesQuery;
use App\Domain\CertVerificaciones\Contracts\CertVerificacionRepositoryInterface;

class GetCertVerificacionesQueryHandler
{
    public function __construct(
        private readonly CertVerificacionRepositoryInterface $repository
    ) {}

    public function handle(GetCertVerificacionesQuery $query): array
    {
        return $this->repository->paginate(
            $query->pagination,
            $query->certificadoId,
            $query->codigoConsultado,
            $query->resultado,
        );
    }
}
