<?php

namespace App\Application\HitosInstitucionales\DTOs;

final readonly class HitoInstitucionalDTO
{
    public function __construct(
        public int     $id,
        public string  $anio,
        public string  $titulo,
        public ?string $descripcion,
        public ?string $imagen_url,
        public ?string $imagen_alt,
        public int     $orden,
        public bool    $activo,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id:          $m->id,
            anio:        $m->anio,
            titulo:      $m->titulo,
            descripcion: $m->descripcion ?? null,
            imagen_url:  $m->imagen_url ?? null,
            imagen_alt:  $m->imagen_alt ?? null,
            orden:       (int) ($m->orden ?? 0),
            activo:      (bool) ($m->activo ?? true),
            created_at:  $m->created_at?->toIso8601String(),
            updated_at:  $m->updated_at?->toIso8601String(),
        );
    }
}
