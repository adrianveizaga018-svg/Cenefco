<?php

namespace App\Application\Areas\QueryHandlers;

use App\Application\Areas\Queries\GetAreasActivasQuery;
use App\Domain\Areas\Contracts\AreaRepositoryInterface;

class GetAreasActivasQueryHandler
{
    public function __construct(
        private readonly AreaRepositoryInterface $repository,
    ) {}

    public function handle(GetAreasActivasQuery $query): array
    {
        return $this->repository->listActivasConTotalCursos();
    }
}
