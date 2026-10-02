<?php

namespace App\Application\CategoriasCampo\Queries;

final readonly class GetCamposByCategoriaQuery
{
    public function __construct(
        public int $categoria_id,
    ) {}
}
