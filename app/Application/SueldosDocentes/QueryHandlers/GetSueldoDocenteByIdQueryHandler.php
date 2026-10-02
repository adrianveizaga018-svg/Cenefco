<?php

namespace App\Application\SueldosDocentes\QueryHandlers;

use App\Application\SueldosDocentes\DTOs\SueldoDocenteDTO;
use App\Application\SueldosDocentes\Queries\GetSueldoDocenteByIdQuery;
use App\Domain\SueldosDocentes\Contracts\SueldoDocenteRepositoryInterface;

class GetSueldoDocenteByIdQueryHandler
{
    public function __construct(
        private readonly SueldoDocenteRepositoryInterface $repository,
    ) {}

    public function handle(GetSueldoDocenteByIdQuery $query): SueldoDocenteDTO
    {
        return $this->repository->findById($query->id);
    }
}
