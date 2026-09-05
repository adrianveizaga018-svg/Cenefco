<?php

namespace App\Application\PlanesAcademicos\Queries;

final readonly class GetPlanAcademicoByIdQuery
{
    public function __construct(public int $id) {}
}
