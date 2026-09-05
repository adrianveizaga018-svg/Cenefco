<?php

namespace App\Application\Revistas\QueryHandlers;

use App\Application\Revistas\DTOs\RevistaDTO;
use App\Application\Revistas\Queries\GetRevistaByIdQuery;
use App\Domain\Revistas\Contracts\RevistaRepositoryInterface;

class GetRevistaByIdQueryHandler
{
    public function __construct(private readonly RevistaRepositoryInterface $repository) {}

    public function handle(GetRevistaByIdQuery $query): RevistaDTO
    {
        return RevistaDTO::fromRow($this->repository->findById($query->id));
    }
}
