<?php

namespace App\Application\Profesiones\QueryHandlers;

use App\Application\Profesiones\DTOs\ProfesionDTO;
use App\Application\Profesiones\Queries\GetProfesionByIdQuery;
use App\Domain\Profesiones\Contracts\ProfesionRepositoryInterface;

class GetProfesionByIdQueryHandler
{
    public function __construct(private readonly ProfesionRepositoryInterface $repository) {}

    public function handle(GetProfesionByIdQuery $query): ProfesionDTO
    {
        return ProfesionDTO::fromRow($this->repository->findById($query->id));
    }
}
