<?php

namespace App\Application\Areas\QueryHandlers;

use App\Application\Areas\DTOs\AreaDTO;
use App\Application\Areas\Queries\GetAreaByIdQuery;
use App\Domain\Areas\Contracts\AreaRepositoryInterface;

class GetAreaByIdQueryHandler
{
    public function __construct(
        private readonly AreaRepositoryInterface $repository,
    ) {}

    public function handle(GetAreaByIdQuery $query): AreaDTO
    {
        return $this->repository->findById($query->id);
    }
}
