<?php

namespace App\Application\BloquesPlantilla\Commands;

final readonly class UpdateBloquePlantillaCommand
{
    public function __construct(
        public int $idBloqueplantilla,
        public ?string $nombre,
        public ?string $descripcion,
    ) {}
}
