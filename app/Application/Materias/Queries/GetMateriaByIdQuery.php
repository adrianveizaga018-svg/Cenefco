<?php

namespace App\Application\Materias\Queries;

final readonly class GetMateriaByIdQuery
{
    public function __construct(public int $id) {}
}
