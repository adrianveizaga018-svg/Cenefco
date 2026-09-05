<?php

namespace App\Application\Materias\QueryHandlers;

use App\Application\Materias\Queries\GetMateriasQuery;
use App\Domain\Materias\Contracts\MateriaRepositoryInterface;

class GetMateriasQueryHandler
{
    public function __construct(
        private readonly MateriaRepositoryInterface $repository
    ) {}

    public function handle(GetMateriasQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->conInactivos);
    }
}
