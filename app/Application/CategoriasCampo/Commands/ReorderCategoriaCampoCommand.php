<?php

namespace App\Application\CategoriasCampo\Commands;

final readonly class ReorderCategoriaCampoCommand
{
    
    public function __construct(
        public int $categoria_id,
        public array $items,
    ) {}
}
