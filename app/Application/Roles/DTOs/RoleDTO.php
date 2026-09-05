<?php

namespace App\Application\Roles\DTOs;

final readonly class RoleDTO
{
    public function __construct(
        public int $id,
        public string $nombre,
        public ?string $descripcion,
        public array $permisos,
        public bool $activo,
        public bool $restringidoAVendedor,
    ) {}

    public static function fromModel(object $role): self
    {
        return new self(
            id:          $role->id,
            nombre:      $role->nombre,
            descripcion: $role->descripcion ?? null,
            permisos:    $role->relationLoaded('permisos')
                ? $role->permisos->pluck('codigo')->all()
                : [],
            activo:      (bool) $role->activo,
            restringidoAVendedor: (bool) ($role->restringido_a_vendedor ?? false),
        );
    }
}
