<?php

namespace App\Application\Resenas\QueryHandlers;

use App\Application\Resenas\DTOs\ResenaDTO;
use App\Application\Resenas\Queries\GetResenaByIdQuery;
use App\Domain\Resenas\Contracts\ResenaRepositoryInterface;

class GetResenaByIdQueryHandler
{
    public function __construct(
        private readonly ResenaRepositoryInterface $repository,
    ) {}

    public function handle(GetResenaByIdQuery $query): ResenaDTO
    {
        return $this->repository->findById($query->id);
    }
}
