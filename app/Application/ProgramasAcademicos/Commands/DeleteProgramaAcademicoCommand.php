<?php

namespace App\Application\ProgramasAcademicos\Commands;

final readonly class DeleteProgramaAcademicoCommand
{
    public function __construct(public int $id) {}
}
