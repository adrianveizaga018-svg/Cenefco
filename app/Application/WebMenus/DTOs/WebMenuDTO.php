<?php

namespace App\Application\WebMenus\DTOs;

final readonly class WebMenuDTO
{
    public function __construct(
        public int $id,
        public string $nombre,
        public ?string $descripcion,
        public bool $activo,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id: $row->id,
            nombre: $row->nombre,
            descripcion: $row->descripcion ?? null,
            activo: (bool) $row->activo,
        );
    }
}
