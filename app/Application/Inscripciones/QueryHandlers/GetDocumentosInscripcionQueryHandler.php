<?php

namespace App\Application\Inscripciones\QueryHandlers;

use App\Application\Inscripciones\DTOs\DocumentosInscripcionDTO;
use App\Application\Inscripciones\Queries\GetDocumentosInscripcionQuery;
use App\Domain\Inscripciones\Contracts\InscripcionRepositoryInterface;

class GetDocumentosInscripcionQueryHandler
{
    public function __construct(
        private readonly InscripcionRepositoryInterface $repository
    ) {}

    public function handle(GetDocumentosInscripcionQuery $query): DocumentosInscripcionDTO
    {
        return $this->repository->getDocumentos($query->idIns);
    }
}
