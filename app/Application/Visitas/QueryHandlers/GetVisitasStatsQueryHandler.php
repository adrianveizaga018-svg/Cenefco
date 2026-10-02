<?php

namespace App\Application\Visitas\QueryHandlers;

use App\Application\Visitas\Queries\GetVisitasStatsQuery;
use App\Domain\Visitas\Contracts\VisitaRepositoryInterface;

class GetVisitasStatsQueryHandler
{
    public function __construct(
        private readonly VisitaRepositoryInterface $repository
    ) {}

    public function handle(GetVisitasStatsQuery $query): array
    {
        return $this->repository->stats($query->desde, $query->hasta);
    }
}
