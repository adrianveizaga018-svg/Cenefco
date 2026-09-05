<?php

namespace App\Application\Profesiones\DTOs;

final readonly class ProfesionDTO
{
    public function __construct(
        public int     $id,
        public string  $nombre,
        public int     $orden,
        public bool    $activo,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id:         $row->id,
            nombre:     $row->nombre,
            orden:      (int) ($row->orden ?? 0),
            activo:     (bool) ($row->activo ?? true),
            created_at: $row->created_at ?? null,
            updated_at: $row->updated_at ?? null,
        );
    }
}
