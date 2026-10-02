<?php

namespace App\Application\Visitas\Queries;

final readonly class GetVisitasStatsQuery
{
    public function __construct(
        public string $desde,
        public string $hasta,
    ) {}
}
