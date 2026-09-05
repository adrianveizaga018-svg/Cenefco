<?php

namespace App\Application\CifrasInstitucionales\QueryHandlers;

use App\Application\CifrasInstitucionales\DTOs\CifraInstitucionalDTO;
use App\Application\CifrasInstitucionales\Queries\GetCifraInstitucionalByIdQuery;
use App\Domain\CifrasInstitucionales\Contracts\CifraInstitucionalRepositoryInterface;

class GetCifraInstitucionalByIdQueryHandler
{
    public function __construct(private readonly CifraInstitucionalRepositoryInterface $repository) {}

    public function handle(GetCifraInstitucionalByIdQuery $q): CifraInstitucionalDTO
    {
        return $this->repository->findById($q->id);
    }
}
