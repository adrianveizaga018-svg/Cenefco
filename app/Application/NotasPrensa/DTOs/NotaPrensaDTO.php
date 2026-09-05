<?php

declare(strict_types=1);

namespace App\Application\NotasPrensa\DTOs;

final readonly class NotaPrensaDTO
{
    public function __construct(
        public int $id,
        public string $titulo,
        public string $medio,
        public ?string $logo_medio_url,
        public ?string $logo_medio_alt,
        public ?string $resumen,
        public ?string $url_noticia,
        public ?string $fecha_publicacion,
        public bool $destacada,
        public int $orden,
        public bool $activo,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id: $m->id,
            titulo: $m->titulo,
            medio: $m->medio,
            logo_medio_url: $m->logo_medio_url,
            logo_medio_alt: $m->logo_medio_alt,
            resumen: $m->resumen,
            url_noticia: $m->url_noticia,
            fecha_publicacion: $m->fecha_publicacion ? (string) $m->fecha_publicacion : null,
            destacada: (bool) $m->destacada,
            orden: (int) $m->orden,
            activo: (bool) $m->activo,
            created_at: $m->created_at?->toIso8601String(),
            updated_at: $m->updated_at?->toIso8601String(),
        );
    }
}
