<?php

namespace App\Application\Planillas\Commands;

final readonly class DeletePlanillaCommand
{
    public function __construct(public int $id) {}
}
