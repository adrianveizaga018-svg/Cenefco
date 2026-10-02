<?php

namespace App\Application\CategoriasCampo\Queries;

final readonly class GetCategoriaCampoByIdQuery
{
    public function __construct(
        public int $categoria_id,
        public int $id,
    ) {}
}
