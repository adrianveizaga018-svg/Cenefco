<?php

namespace App\Application\Cartas\QueryHandlers;

use App\Application\Cartas\DTOs\CartaDTO;
use App\Application\Cartas\Queries\GetCartaByIdQuery;
use App\Domain\Cartas\Contracts\CartaRepositoryInterface;

class GetCartaByIdQueryHandler
{
    public function __construct(private readonly CartaRepositoryInterface $repository) {}

    public function handle(GetCartaByIdQuery $query): CartaDTO
    {
        return CartaDTO::fromRow($this->repository->findById($query->id));
    }
}
