<?php

namespace App\Application\Inscripciones\QueryHandlers;

use App\Application\Inscripciones\DTOs\InscripcionDetalleDTO;
use App\Application\Inscripciones\Queries\GetInscripcionDetalleQuery;
use App\Domain\Inscripciones\Contracts\InscripcionRepositoryInterface;

class GetInscripcionDetalleQueryHandler
{
    public function __construct(
        private readonly InscripcionRepositoryInterface $repository
    ) {}

    public function handle(GetInscripcionDetalleQuery $query): InscripcionDetalleDTO
    {
        return $this->repository->findDetalleById($query->id);
    }
}
