<?php

namespace App\Application\CategoriasNoticia\QueryHandlers;

use App\Application\CategoriasNoticia\DTOs\CategoriaNoticiaDTO;
use App\Application\CategoriasNoticia\Queries\GetCategoriaNoticiaByIdQuery;
use App\Domain\CategoriasNoticia\Contracts\CategoriaNoticiaRepositoryInterface;

class GetCategoriaNoticiaByIdQueryHandler
{
    public function __construct(
        private readonly CategoriaNoticiaRepositoryInterface $repository
    ) {}

    public function handle(GetCategoriaNoticiaByIdQuery $query): CategoriaNoticiaDTO
    {
        return $this->repository->findById($query->id);
    }
}
