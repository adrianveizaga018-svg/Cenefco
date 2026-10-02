<?php

namespace App\Application\CartaModelos\Queries;

final readonly class GetCartaModeloByIdQuery
{
    public function __construct(public int $id) {}
}
