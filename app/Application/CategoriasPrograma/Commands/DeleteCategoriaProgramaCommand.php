<?php

namespace App\Application\CategoriasPrograma\Commands;

final readonly class DeleteCategoriaProgramaCommand
{
    public function __construct(
        public int $id,
    ) {}
}
