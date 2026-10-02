<?php

namespace App\Application\CartaGens\Queries;

final readonly class GetCartaGenByIdQuery
{
    public function __construct(public int $id) {}
}
