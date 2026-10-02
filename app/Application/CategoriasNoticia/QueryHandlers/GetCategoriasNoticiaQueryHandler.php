<?php

namespace App\Application\CategoriasNoticia\QueryHandlers;

use App\Application\CategoriasNoticia\Queries\GetCategoriasNoticiaQuery;
use App\Domain\CategoriasNoticia\Contracts\CategoriaNoticiaRepositoryInterface;

class GetCategoriasNoticiaQueryHandler
{
    public function __construct(
        private readonly CategoriaNoticiaRepositoryInterface $repository
    ) {}

    public function handle(GetCategoriasNoticiaQuery $query): array
    {
        return $this->repository->paginate($query->pagination);
    }
}
