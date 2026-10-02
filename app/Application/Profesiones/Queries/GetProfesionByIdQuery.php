<?php

namespace App\Application\Profesiones\Queries;

final readonly class GetProfesionByIdQuery
{
    public function __construct(public int $id) {}
}
