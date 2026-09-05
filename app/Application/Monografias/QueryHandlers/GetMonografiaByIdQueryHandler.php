<?php

namespace App\Application\Monografias\QueryHandlers;

use App\Application\Monografias\DTOs\MonografiaDTO;
use App\Application\Monografias\Queries\GetMonografiaByIdQuery;
use App\Domain\Monografias\Contracts\MonografiaRepositoryInterface;

class GetMonografiaByIdQueryHandler
{
    public function __construct(private readonly MonografiaRepositoryInterface $repository) {}

    public function handle(GetMonografiaByIdQuery $query): MonografiaDTO
    {
        return MonografiaDTO::fromRow($this->repository->findById($query->id));
    }
}
