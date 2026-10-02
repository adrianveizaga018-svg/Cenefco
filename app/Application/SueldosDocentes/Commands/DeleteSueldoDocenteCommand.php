<?php

namespace App\Application\SueldosDocentes\Commands;

final readonly class DeleteSueldoDocenteCommand
{
    public function __construct(public int $id) {}
}
