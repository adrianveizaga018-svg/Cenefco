<?php

namespace App\Application\Asesores\Queries;

final readonly class GetAsesorByIdQuery
{
    public function __construct(public int $id) {}
}
