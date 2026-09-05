<?php

namespace App\Application\Normas\QueryHandlers;

use App\Application\Normas\Queries\GetPaginatedNormasQuery;
use App\Domain\Normas\Contracts\NormaRepositoryInterface;

class GetPaginatedNormasQueryHandler
{
    public function __construct(private readonly NormaRepositoryInterface $repository) {}

    public function handle(GetPaginatedNormasQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->filters);
    }
}
