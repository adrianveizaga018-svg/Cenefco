<?php

namespace App\Application\CertConfigProgramas\QueryHandlers;

use App\Application\CertConfigProgramas\DTOs\CertSolicitudDTO;
use App\Application\CertConfigProgramas\Queries\GetSolicitudesQuery;
use App\Domain\CertConfigProgramas\Contracts\CertSolicitudRepositoryInterface;

class GetSolicitudesQueryHandler
{
    public function __construct(
        private readonly CertSolicitudRepositoryInterface $repo,
    ) {}

    public function handle(GetSolicitudesQuery $query): array
    {
        return $this->repo->paginate(
            $query->pageSize,
            $query->page,
            $query->estado,
            $query->programa_id,
        );
    }
}
