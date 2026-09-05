<?php

namespace App\Application\Universidades\Queries;

final readonly class GetUniversidadByIdQuery
{
    public function __construct(public int $id) {}
}
