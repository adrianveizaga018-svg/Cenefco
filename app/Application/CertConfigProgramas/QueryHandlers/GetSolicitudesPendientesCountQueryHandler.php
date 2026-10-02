<?php

namespace App\Application\CertConfigProgramas\QueryHandlers;

use App\Application\CertConfigProgramas\Queries\GetSolicitudesPendientesCountQuery;
use App\Domain\CertConfigProgramas\Contracts\CertSolicitudRepositoryInterface;

class GetSolicitudesPendientesCountQueryHandler
{
    public function __construct(
        private readonly CertSolicitudRepositoryInterface $repo,
    ) {}

    public function handle(GetSolicitudesPendientesCountQuery $query): int
    {
        return $this->repo->countPendientes();
    }
}
