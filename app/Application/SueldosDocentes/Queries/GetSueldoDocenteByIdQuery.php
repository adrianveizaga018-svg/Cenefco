<?php

namespace App\Application\SueldosDocentes\Queries;

final readonly class GetSueldoDocenteByIdQuery
{
    public function __construct(public int $id) {}
}
