<?php

namespace App\Application\Convenios\QueryHandlers;

use App\Application\Convenios\DTOs\ConvenioDTO;
use App\Application\Convenios\Queries\GetConvenioByIdQuery;
use App\Domain\Convenios\Contracts\ConvenioRepositoryInterface;

class GetConvenioByIdQueryHandler
{
    public function __construct(
        private readonly ConvenioRepositoryInterface $repository,
    ) {}

    public function handle(GetConvenioByIdQuery $query): ConvenioDTO
    {
        return $this->repository->findById($query->id);
    }
}
