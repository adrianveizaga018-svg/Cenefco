<?php

namespace App\Application\BloquesPlantilla\Commands;

final readonly class CreateBloquePlantillaCommand
{
    public function __construct(
        public int $idBloqueplantilla,
        public ?string $nombre,
        public ?string $descripcion,
        public int $idUsReg,
    ) {}
}
