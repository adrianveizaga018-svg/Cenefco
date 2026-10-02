<?php

namespace App\Application\FechasDoc\QueryHandlers;

use App\Application\FechasDoc\DTOs\FechaDocDTO;
use App\Application\FechasDoc\Queries\GetFechaDocByIdQuery;
use App\Domain\FechasDoc\Contracts\FechaDocRepositoryInterface;

class GetFechaDocByIdQueryHandler
{
    public function __construct(
        private readonly FechaDocRepositoryInterface $repository
    ) {}

    public function handle(GetFechaDocByIdQuery $query): FechaDocDTO
    {
        return $this->repository->findById($query->id);
    }
}
