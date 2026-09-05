<?php

namespace App\Application\Convenios\QueryHandlers;

use App\Application\Convenios\Queries\GetConveniosQuery;
use App\Domain\Convenios\Contracts\ConvenioRepositoryInterface;

class GetConveniosQueryHandler
{
    public function __construct(
        private readonly ConvenioRepositoryInterface $repository,
    ) {}

    public function handle(GetConveniosQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->soloActivos);
    }
}
