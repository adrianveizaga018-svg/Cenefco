<?php

namespace App\Application\Inscripciones\QueryHandlers;

use App\Application\Inscripciones\Queries\GetInscripcionesQuery;
use App\Domain\Inscripciones\Contracts\InscripcionRepositoryInterface;

class GetInscripcionesQueryHandler
{
    public function __construct(
        private readonly InscripcionRepositoryInterface $repository
    ) {}

    public function handle(GetInscripcionesQuery $query): array
    {
        return $this->repository->paginate(
            $query->pagination,
            $query->conInactivos,
            $query->idUs,
            $query->idImp,
            $query->programaId,
            $query->periodo,
            $query->gestion,
            $query->idImpPermitidos,
        );
    }
}
