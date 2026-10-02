<?php

namespace App\Application\CategoriasNoticia\Queries;

final readonly class GetCategoriaNoticiaByIdQuery
{
    public function __construct(public int $id) {}
}
