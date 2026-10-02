<?php

namespace App\Application\CategoriasNoticia\Commands;

final readonly class UpdateCategoriaNoticiaCommand
{
    public function __construct(
        public int $id,
        public array $data,
    ) {}
}
