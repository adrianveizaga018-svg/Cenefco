<?php

namespace App\Application\Horarios\Queries;

final readonly class GetHorarioByIdQuery
{
    public function __construct(public int $id) {}
}
