<?php

namespace App\Application\Faqs\DTOs;

final readonly class FaqDTO
{
    public function __construct(
        public int     $id,
        public string  $pregunta,
        public string  $respuesta,
        public ?string $categoria,
        public ?int    $programa_id,
        public int     $orden,
        public bool    $activo,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id:          $m->id,
            pregunta:    $m->pregunta,
            respuesta:   $m->respuesta,
            categoria:   $m->categoria ?? null,
            programa_id: $m->programa_id ? (int) $m->programa_id : null,
            orden:       (int) ($m->orden ?? 0),
            activo:      (bool) ($m->activo ?? true),
            created_at:  $m->created_at?->toIso8601String(),
            updated_at:  $m->updated_at?->toIso8601String(),
        );
    }
}
