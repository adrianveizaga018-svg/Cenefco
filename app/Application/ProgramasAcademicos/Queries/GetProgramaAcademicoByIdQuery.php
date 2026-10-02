<?php

namespace App\Application\ProgramasAcademicos\Queries;

final readonly class GetProgramaAcademicoByIdQuery
{
    public function __construct(public int $id) {}
}
