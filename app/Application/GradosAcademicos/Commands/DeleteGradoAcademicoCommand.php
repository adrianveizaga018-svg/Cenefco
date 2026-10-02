<?php

namespace App\Application\GradosAcademicos\Commands;

final readonly class DeleteGradoAcademicoCommand
{
    public function __construct(public int $id) {}
}
