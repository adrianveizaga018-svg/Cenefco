<?php

namespace App\Application\Formularios\DTOs;

final readonly class FormularioDTO
{
    public function __construct(
        public int     $id,
        public string  $nombre,
        public string  $slug,
        public ?string $descripcion,
        public array   $campos,
        public bool    $activo,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromModel(object $m): self
    {
        $campos = $m->campos;
        if (is_string($campos)) {
            $campos = json_decode($campos, true) ?? [];
        }

        return new self(
            id:          (int) $m->id,
            nombre:      $m->nombre,
            slug:        $m->slug,
            descripcion: $m->descripcion ?? null,
            campos:      (array) $campos,
            activo:      (bool) $m->activo,
            created_at:  $m->created_at ? (string) $m->created_at : null,
            updated_at:  $m->updated_at ? (string) $m->updated_at : null,
        );
    }
}
