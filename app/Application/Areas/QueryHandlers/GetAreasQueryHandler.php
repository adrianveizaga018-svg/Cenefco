<?php

namespace App\Application\Areas\QueryHandlers;

use App\Application\Areas\Queries\GetAreasQuery;
use App\Domain\Areas\Contracts\AreaRepositoryInterface;

class GetAreasQueryHandler
{
    public function __construct(
        private readonly AreaRepositoryInterface $repository,
    ) {}

    public function handle(GetAreasQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->soloActivas);
    }
}
