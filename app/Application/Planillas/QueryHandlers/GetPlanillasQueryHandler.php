<?php

namespace App\Application\Planillas\QueryHandlers;

use App\Application\Planillas\Queries\GetPlanillasQuery;
use App\Domain\Planillas\Contracts\PlanillaRepositoryInterface;

class GetPlanillasQueryHandler
{
    public function __construct(private readonly PlanillaRepositoryInterface $repository) {}

    public function handle(GetPlanillasQuery $query): array
    {
        return $this->repository->paginate($query->pagination);
    }
}
