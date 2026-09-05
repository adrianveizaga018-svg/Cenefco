<?php

namespace App\Application\Articulos\QueryHandlers;

use App\Application\Articulos\DTOs\ArticuloDTO;
use App\Application\Articulos\Queries\GetArticuloByIdQuery;
use App\Domain\Articulos\Contracts\ArticuloRepositoryInterface;

class GetArticuloByIdQueryHandler
{
    public function __construct(private readonly ArticuloRepositoryInterface $repository) {}

    public function handle(GetArticuloByIdQuery $query): ArticuloDTO
    {
        return ArticuloDTO::fromRow($this->repository->findById($query->id));
    }
}
