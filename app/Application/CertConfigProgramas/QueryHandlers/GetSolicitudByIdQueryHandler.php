<?php

namespace App\Application\CertConfigProgramas\QueryHandlers;

use App\Application\CertConfigProgramas\DTOs\CertSolicitudDTO;
use App\Application\CertConfigProgramas\Queries\GetSolicitudByIdQuery;
use App\Domain\CertConfigProgramas\Contracts\CertSolicitudRepositoryInterface;
use App\Domain\CertConfigProgramas\Exceptions\CertSolicitudNotFoundException;

class GetSolicitudByIdQueryHandler
{
    public function __construct(
        private readonly CertSolicitudRepositoryInterface $repo,
    ) {}

    public function handle(GetSolicitudByIdQuery $query): CertSolicitudDTO
    {
        $row = $this->repo->findById($query->id);
        if (! $row) {
            throw new CertSolicitudNotFoundException($query->id);
        }

        return CertSolicitudDTO::fromRow($row);
    }
}
