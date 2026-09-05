<?php

namespace App\Application\Articulos\QueryHandlers;

use App\Application\Articulos\DTOs\ArticuloDTO;
use App\Application\Articulos\Queries\GetArticuloBySlugQuery;
use App\Domain\Articulos\Contracts\ArticuloRepositoryInterface;

class GetArticuloBySlugQueryHandler
{
    public function __construct(private readonly ArticuloRepositoryInterface $repository) {}

    public function handle(GetArticuloBySlugQuery $query): ArticuloDTO
    {
        return ArticuloDTO::fromRow($this->repository->findBySlug($query->slug));
    }
}
