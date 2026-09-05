<?php

namespace App\Application\CategoriasCampo\QueryHandlers;

use App\Application\CategoriasCampo\DTOs\CategoriaCampoDTO;
use App\Application\CategoriasCampo\Queries\GetCategoriaCampoByIdQuery;
use App\Domain\CategoriasCampo\Contracts\CategoriaCampoRepositoryInterface;

class GetCategoriaCampoByIdQueryHandler
{
    public function __construct(
        private readonly CategoriaCampoRepositoryInterface $repository,
    ) {}

    public function handle(GetCategoriaCampoByIdQuery $query): CategoriaCampoDTO
    {
        return $this->repository->findByCategoriaAndId($query->categoria_id, $query->id);
    }
}
