<?php

namespace App\Application\CifrasInstitucionales\DTOs;

final readonly class CifraInstitucionalDTO
{
    public function __construct(
        public int     $id,
        public string  $valor,
        public string  $etiqueta,
        public ?string $descripcion,
        public ?string $icono,
        public ?string $color,
        public int     $orden,
        public bool    $activo,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id:          $m->id,
            valor:       $m->valor,
            etiqueta:    $m->etiqueta,
            descripcion: $m->descripcion ?? null,
            icono:       $m->icono ?? null,
            color:       $m->color ?? null,
            orden:       (int) ($m->orden ?? 0),
            activo:      (bool) ($m->activo ?? true),
            created_at:  $m->created_at?->toIso8601String(),
            updated_at:  $m->updated_at?->toIso8601String(),
        );
    }
}
