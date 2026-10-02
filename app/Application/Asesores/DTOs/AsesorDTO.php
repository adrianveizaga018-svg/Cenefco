<?php

namespace App\Application\Asesores\DTOs;

final readonly class AsesorDTO
{
    public function __construct(
        public int $id,
        public string $nombre,
        public string $telefono,
        public ?string $email,
        public ?string $especialidad,
        public bool $disponible,
        public bool $activo,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id:          (int) $row->id,
            nombre:      $row->nombre,
            telefono:    $row->telefono,
            email:       $row->email ?? null,
            especialidad:$row->especialidad ?? null,
            disponible:  (bool) ($row->disponible ?? true),
            activo:      (bool) ($row->activo ?? true),
            createdAt:   isset($row->created_at) ? (string) $row->created_at : null,
            updatedAt:   isset($row->updated_at) ? (string) $row->updated_at : null,
        );
    }
}
