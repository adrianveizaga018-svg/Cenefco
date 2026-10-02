<?php

namespace App\Application\GradosAcademicos\Queries;

final readonly class GetGradoAcademicoByIdQuery
{
    public function __construct(public int $id) {}
}
