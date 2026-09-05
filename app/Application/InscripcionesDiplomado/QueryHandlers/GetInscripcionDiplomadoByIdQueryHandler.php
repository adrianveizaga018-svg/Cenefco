<?php

namespace App\Application\InscripcionesDiplomado\QueryHandlers;

use App\Application\InscripcionesDiplomado\DTOs\InscripcionDiplomadoDTO;
use App\Application\InscripcionesDiplomado\Queries\GetInscripcionDiplomadoByIdQuery;
use App\Domain\InscripcionesDiplomado\Contracts\InscripcionDiplomadoRepositoryInterface;

class GetInscripcionDiplomadoByIdQueryHandler
{
    public function __construct(
        private readonly InscripcionDiplomadoRepositoryInterface $repository,
    ) {}

    public function handle(GetInscripcionDiplomadoByIdQuery $query): InscripcionDiplomadoDTO
    {
        return $this->repository->findById($query->id);
    }
}
