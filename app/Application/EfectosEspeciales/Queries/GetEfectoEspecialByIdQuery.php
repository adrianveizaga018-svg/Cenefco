<?php

namespace App\Application\EfectosEspeciales\Queries;

final readonly class GetEfectoEspecialByIdQuery
{
    public function __construct(public int $id) {}
}
