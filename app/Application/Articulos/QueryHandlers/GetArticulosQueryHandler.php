<?php

namespace App\Application\Articulos\QueryHandlers;

use App\Application\Articulos\Queries\GetArticulosQuery;
use App\Domain\Articulos\Contracts\ArticuloRepositoryInterface;

class GetArticulosQueryHandler
{
    public function __construct(private readonly ArticuloRepositoryInterface $repository) {}

    public function handle(GetArticulosQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->query, $query->estadoWeb, $query->conInactivos);
    }
}
