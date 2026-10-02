<?php

namespace App\Application\CitasAsesoria\QueryHandlers;

use App\Application\CitasAsesoria\DTOs\CitaAsesoriaDTO;
use App\Application\CitasAsesoria\Queries\GetCitaAsesoriaByIdQuery;
use App\Domain\CitasAsesoria\Contracts\CitaAsesoriaRepositoryInterface;

class GetCitaAsesoriaByIdQueryHandler
{
    public function __construct(
        private readonly CitaAsesoriaRepositoryInterface $repository,
    ) {}

    public function handle(GetCitaAsesoriaByIdQuery $query): CitaAsesoriaDTO
    {
        return CitaAsesoriaDTO::fromRow($this->repository->findById($query->id));
    }
}
