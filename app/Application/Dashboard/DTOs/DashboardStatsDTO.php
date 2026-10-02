<?php

namespace App\Application\Dashboard\DTOs;

final readonly class DashboardStatsDTO
{
    public function __construct(public array $stats) {}

    public static function fromArray(array $stats): self
    {
        return new self(stats: $stats);
    }
}
