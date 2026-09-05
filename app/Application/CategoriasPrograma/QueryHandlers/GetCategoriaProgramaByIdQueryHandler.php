<?php

namespace App\Application\CategoriasPrograma\QueryHandlers;

use App\Application\CategoriasPrograma\DTOs\CategoriaProgramaDTO;
use App\Application\CategoriasPrograma\Queries\GetCategoriaProgramaByIdQuery;
use App\Domain\CategoriasPrograma\Contracts\CategoriaProgramaRepositoryInterface;

class GetCategoriaProgramaByIdQueryHandler
{
    public function __construct(
        private readonly CategoriaProgramaRepositoryInterface $repository,
    ) {}

    public function handle(GetCategoriaProgramaByIdQuery $query): CategoriaProgramaDTO
    {
        return $this->repository->findById($query->id);
    }
}
