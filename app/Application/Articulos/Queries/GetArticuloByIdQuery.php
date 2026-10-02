<?php

namespace App\Application\Articulos\Queries;

final readonly class GetArticuloByIdQuery
{
    public function __construct(public int $id) {}
}
