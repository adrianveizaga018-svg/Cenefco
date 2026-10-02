<?php

namespace App\Application\CertVerificaciones\QueryHandlers;

use App\Application\CertVerificaciones\DTOs\CertVerificacionDTO;
use App\Application\CertVerificaciones\Queries\GetCertVerificacionByIdQuery;
use App\Domain\CertVerificaciones\Contracts\CertVerificacionRepositoryInterface;

class GetCertVerificacionByIdQueryHandler
{
    public function __construct(
        private readonly CertVerificacionRepositoryInterface $repository
    ) {}

    public function handle(GetCertVerificacionByIdQuery $query): CertVerificacionDTO
    {
        return CertVerificacionDTO::fromRow($this->repository->findById($query->id));
    }
}
