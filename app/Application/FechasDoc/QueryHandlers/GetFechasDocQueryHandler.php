<?php

namespace App\Application\FechasDoc\QueryHandlers;

use App\Application\FechasDoc\Queries\GetFechasDocQuery;
use App\Domain\FechasDoc\Contracts\FechaDocRepositoryInterface;

class GetFechasDocQueryHandler
{
    public function __construct(
        private readonly FechaDocRepositoryInterface $repository
    ) {}

    public function handle(GetFechasDocQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->conInactivos, $query->idPlandoc);
    }
}
