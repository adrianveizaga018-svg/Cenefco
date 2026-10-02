<?php

namespace App\Application\CategoriasPrograma\Queries;

final readonly class GetCategoriaProgramaByIdQuery
{
    public function __construct(
        public int $id,
    ) {}
}
