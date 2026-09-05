<?php

namespace App\Application\Inscripciones\QueryHandlers;

use App\Application\Inscripciones\DTOs\InscripcionDTO;
use App\Application\Inscripciones\Queries\GetInscripcionByIdQuery;
use App\Domain\Inscripciones\Contracts\InscripcionRepositoryInterface;

class GetInscripcionByIdQueryHandler
{
    public function __construct(
        private readonly InscripcionRepositoryInterface $repository
    ) {}

    public function handle(GetInscripcionByIdQuery $query): InscripcionDTO
    {
        return $this->repository->findById($query->id);
    }
}
