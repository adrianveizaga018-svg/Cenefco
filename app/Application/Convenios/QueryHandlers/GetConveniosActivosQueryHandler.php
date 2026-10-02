<?php

namespace App\Application\Convenios\QueryHandlers;

use App\Application\Convenios\Queries\GetConveniosActivosQuery;
use App\Domain\Convenios\Contracts\ConvenioRepositoryInterface;

class GetConveniosActivosQueryHandler
{
    public function __construct(
        private readonly ConvenioRepositoryInterface $repository,
    ) {}

    public function handle(GetConveniosActivosQuery $query): array
    {
        return $this->repository->all();
    }
}
