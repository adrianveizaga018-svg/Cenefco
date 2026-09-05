<?php
namespace App\Application\PlanDoc\QueryHandlers;
use App\Application\PlanDoc\Queries\GetPlanDocQuery;
use App\Domain\PlanDoc\Contracts\PlanDocRepositoryInterface;
class GetPlanDocQueryHandler {
    public function __construct(private readonly PlanDocRepositoryInterface $repository) {}
    public function handle(GetPlanDocQuery $query): array {
        return $this->repository->paginate($query->pagination, $query->query, $query->conInactivos);
    }
}
