<?php

namespace App\Application\BloquesPlantilla\DTOs;

final readonly class BloquePlantillaDTO
{
    public function __construct(
        public int $idBloqueplantilla,
        public ?string $nombre,
        public ?string $descripcion,
        public int $estado,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            idBloqueplantilla: $row->id_bloqueplantilla,
            nombre: $row->nombre ?? null,
            descripcion: $row->descripcion ?? null,
            estado: $row->estado,
        );
    }
}
