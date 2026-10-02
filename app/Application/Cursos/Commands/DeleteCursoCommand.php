<?php

namespace App\Application\Cursos\Commands;

final readonly class DeleteCursoCommand
{
    public function __construct(public int $id) {}
}
