<?php

namespace App\Application\BloquesAjustables\Commands;

final readonly class UpdateBloqueAjustableCommand
{
    public function __construct(
        public int $idBloqueajustable,
        public ?int $idPagina,
        public ?int $idBloqueplantilla,
        public ?string $titulo,
    ) {}
}
