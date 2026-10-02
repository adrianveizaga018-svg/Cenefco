<?php

namespace App\Application\Articulos\Queries;

final readonly class GetArticuloBySlugQuery
{
    public function __construct(public string $slug) {}
}
