<?php

namespace App\Application\CategoriasCampo\QueryHandlers;

use App\Application\CategoriasCampo\Queries\GetCamposByCategoriaQuery;
use App\Domain\CategoriasCampo\Contracts\CategoriaCampoRepositoryInterface;

class GetCamposByCategoriaQueryHandler
{
    public function __construct(
        private readonly CategoriaCampoRepositoryInterface $repository,
    ) {}

    public function handle(GetCamposByCategoriaQuery $query): array
    {
        return $this->repository->findByCategoria($query->categoria_id);
    }
}
