<?php
namespace App\Application\GaleriasCategorias\DTOs;
final readonly class GaleriaCategoriaDTO {
    public function __construct(
        public int $id,
        public string $nombre,
        public string $slug,
        public ?string $descripcion,
        public int $orden,
        public bool $activo,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}
    public static function fromModel(object $m): self {
        return new self(
            id: $m->id,
            nombre: $m->nombre,
            slug: $m->slug,
            descripcion: $m->descripcion ?? null,
            orden: (int)($m->orden ?? 0),
            activo: (bool)($m->activo ?? false),
            created_at: $m->created_at?->toIso8601String(),
            updated_at: $m->updated_at?->toIso8601String(),
        );
    }
}
