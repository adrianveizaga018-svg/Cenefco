<?php

namespace App\Application\Materias\QueryHandlers;

use App\Application\Materias\DTOs\MateriaDTO;
use App\Application\Materias\Queries\GetMateriaByIdQuery;
use App\Domain\Materias\Contracts\MateriaRepositoryInterface;

class GetMateriaByIdQueryHandler
{
    public function __construct(
        private readonly MateriaRepositoryInterface $repository
    ) {}

    public function handle(GetMateriaByIdQuery $query): MateriaDTO
    {
        return $this->repository->findById($query->id);
    }
}
