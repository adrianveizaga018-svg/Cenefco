<?php

namespace App\Application\CartaModelos\QueryHandlers;

use App\Application\CartaModelos\Queries\GetCartaModelosQuery;
use App\Domain\CartaModelos\Contracts\CartaModeloRepositoryInterface;

class GetCartaModelosQueryHandler
{
    public function __construct(private readonly CartaModeloRepositoryInterface $repository) {}

    public function handle(GetCartaModelosQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->query, $query->conInactivos);
    }
}
