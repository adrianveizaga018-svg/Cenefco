<?php

namespace App\Application\PlanesAcademicos\Commands;

final readonly class DeletePlanAcademicoCommand
{
    public function __construct(public int $id) {}
}
