<?php

namespace App\Application\CartaGens\QueryHandlers;

use App\Application\CartaGens\DTOs\CartaGenDTO;
use App\Application\CartaGens\Queries\GetCartaGenByIdQuery;
use App\Domain\CartaGens\Contracts\CartaGenRepositoryInterface;

class GetCartaGenByIdQueryHandler
{
    public function __construct(private readonly CartaGenRepositoryInterface $repository) {}

    public function handle(GetCartaGenByIdQuery $query): CartaGenDTO
    {
        return CartaGenDTO::fromRow($this->repository->findById($query->id));
    }
}
