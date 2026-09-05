<?php

namespace App\Application\CategoriasNoticia\Commands;

final readonly class DeleteCategoriaNoticiaCommand
{
    public function __construct(
        public int $id,
    ) {}
}
