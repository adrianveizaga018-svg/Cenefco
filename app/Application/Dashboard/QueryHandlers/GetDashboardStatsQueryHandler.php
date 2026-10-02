<?php

namespace App\Application\Dashboard\QueryHandlers;

use App\Application\Dashboard\DTOs\DashboardStatsDTO;
use App\Application\Dashboard\Queries\GetDashboardStatsQuery;
use App\Domain\Dashboard\Contracts\DashboardRepositoryInterface;

class GetDashboardStatsQueryHandler
{
    public function __construct(
        private readonly DashboardRepositoryInterface $repository,
    ) {}

    public function handle(GetDashboardStatsQuery $query): DashboardStatsDTO
    {
        return DashboardStatsDTO::fromArray($this->repository->stats());
    }
}
