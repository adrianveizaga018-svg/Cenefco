<?php

namespace App\Application\Planillas\Queries;

final readonly class GetPlanillaByIdQuery
{
    public function __construct(public int $id) {}
}
