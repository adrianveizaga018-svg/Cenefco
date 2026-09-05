<?php

namespace App\Application\CartaModelos\QueryHandlers;

use App\Application\CartaModelos\DTOs\CartaModeloDTO;
use App\Application\CartaModelos\Queries\GetCartaModeloByIdQuery;
use App\Domain\CartaModelos\Contracts\CartaModeloRepositoryInterface;

class GetCartaModeloByIdQueryHandler
{
    public function __construct(private readonly CartaModeloRepositoryInterface $repository) {}

    public function handle(GetCartaModeloByIdQuery $query): CartaModeloDTO
    {
        return CartaModeloDTO::fromRow($this->repository->findById($query->id));
    }
}
