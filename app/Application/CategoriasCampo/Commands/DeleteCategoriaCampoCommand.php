<?php

namespace App\Application\CategoriasCampo\Commands;

final readonly class DeleteCategoriaCampoCommand
{
    public function __construct(
        public int $categoria_id,
        public int $id,
    ) {}
}
