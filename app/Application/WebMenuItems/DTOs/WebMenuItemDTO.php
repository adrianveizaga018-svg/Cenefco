<?php

namespace App\Application\WebMenuItems\DTOs;

final readonly class WebMenuItemDTO
{
    public function __construct(
        public int $id,
        public int $menuId,
        public ?int $parentId,
        public string $titulo,
        public ?string $url,
        public int $orden,
        public bool $activo,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id: $row->id,
            menuId: $row->menu_id,
            parentId: $row->parent_id ?? null,
            titulo: $row->titulo,
            url: $row->url ?? null,
            orden: $row->orden ?? 0,
            activo: (bool) $row->activo,
        );
    }
}
