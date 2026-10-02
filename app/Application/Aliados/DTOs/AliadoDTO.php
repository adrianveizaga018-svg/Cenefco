<?php

namespace App\Application\Aliados\DTOs;

final readonly class AliadoDTO
{
    public function __construct(
        public int     $id,
        public string  $nombre,
        public string  $logo_url,
        public ?string $logo_alt,
        public ?string $url_sitio,
        public ?string $descripcion,
        public ?string $tipo,
        public int     $orden,
        public bool    $activo,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id:          $m->id,
            nombre:      $m->nombre,
            logo_url:    $m->logo_url,
            logo_alt:    $m->logo_alt ?? null,
            url_sitio:   $m->url_sitio ?? null,
            descripcion: $m->descripcion ?? null,
            tipo:        $m->tipo ?? null,
            orden:       (int) ($m->orden ?? 0),
            activo:      (bool) ($m->activo ?? true),
            created_at:  $m->created_at?->toIso8601String(),
            updated_at:  $m->updated_at?->toIso8601String(),
        );
    }
}
