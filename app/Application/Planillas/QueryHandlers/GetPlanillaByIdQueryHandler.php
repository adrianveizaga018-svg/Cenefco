<?php

namespace App\Application\Planillas\QueryHandlers;

use App\Application\Planillas\Queries\GetPlanillaByIdQuery;
use App\Domain\Planillas\Contracts\PlanillaRepositoryInterface;

class GetPlanillaByIdQueryHandler
{
    public function __construct(private readonly PlanillaRepositoryInterface $repository) {}

    public function handle(GetPlanillaByIdQuery $query): mixed
    {
        return $this->repository->findById($query->id);
    }
}
