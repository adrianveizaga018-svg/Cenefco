<?php

namespace App\Domain\Dashboard\Contracts;

interface DashboardRepositoryInterface
{
    public function stats(): array;
}
