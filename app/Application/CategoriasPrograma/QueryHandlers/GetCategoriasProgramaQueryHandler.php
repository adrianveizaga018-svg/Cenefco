<?php

namespace App\Application\CategoriasPrograma\QueryHandlers;

use App\Application\CategoriasPrograma\Queries\GetCategoriasProgramaQuery;
use App\Domain\CategoriasPrograma\Contracts\CategoriaProgramaRepositoryInterface;

class GetCategoriasProgramaQueryHandler
{
    public function __construct(
        private readonly CategoriaProgramaRepositoryInterface $repository,
    ) {}

    public function handle(GetCategoriasProgramaQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->soloActivos);
    }
}
