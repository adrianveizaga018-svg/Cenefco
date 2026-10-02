<?php

namespace App\Application\Etiquetas\Commands;

final readonly class UpdateEtiquetaCommand
{
    public function __construct(
        public int     $id,
        public ?string $nombre = null,
        public ?string $color  = null,
    ) {}
}
