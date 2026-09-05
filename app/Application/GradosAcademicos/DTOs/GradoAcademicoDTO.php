<?php

namespace App\Application\GradosAcademicos\DTOs;

final readonly class GradoAcademicoDTO
{
    public function __construct(
        public int     $id,
        public string  $nombre,
        public string  $abreviatura,
        public bool    $requiere_titulo,
        public int     $orden,
        public bool    $activo,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id:              $row->id,
            nombre:          $row->nombre,
            abreviatura:     $row->abreviatura,
            requiere_titulo: (bool) ($row->requiere_titulo ?? true),
            orden:           (int) ($row->orden ?? 0),
            activo:          (bool) ($row->activo ?? true),
            created_at:      $row->created_at ?? null,
            updated_at:      $row->updated_at ?? null,
        );
    }
}
