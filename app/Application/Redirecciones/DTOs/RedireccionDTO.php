<?php

namespace App\Application\Redirecciones\DTOs;

final readonly class RedireccionDTO
{
    public function __construct(
        public int     $id,
        public string  $url_origen,
        public string  $url_destino,
        public int     $codigo_http,
        public int     $hits,
        public bool    $activo,
        public ?string $notas,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id:          $m->id,
            url_origen:  $m->url_origen,
            url_destino: $m->url_destino,
            codigo_http: (int) $m->codigo_http,
            hits:        (int) $m->hits,
            activo:      (bool) $m->activo,
            notas:       $m->notas ?? null,
            created_at:  (is_string($m->created_at ?? null) ? $m->created_at : $m->created_at?->toIso8601String()),
            updated_at:  (is_string($m->updated_at ?? null) ? $m->updated_at : $m->updated_at?->toIso8601String()),
        );
    }
}
