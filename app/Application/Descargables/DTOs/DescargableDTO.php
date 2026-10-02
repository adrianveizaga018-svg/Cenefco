<?php

declare(strict_types=1);

namespace App\Application\Descargables\DTOs;

final readonly class DescargableDTO
{
    public function __construct(
        public int $id,
        public string $nombre,
        public ?string $tipo,
        public string $archivo_url,
        public ?string $imagen_portada_url,
        public ?int $programa_id,
        public bool $requiere_datos,
        public int $descargas,
        public int $orden,
        public bool $activo,
        public ?string $created_at,
        public ?string $updated_at,
        public ?string $deleted_at,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id: $m->id,
            nombre: $m->nombre,
            tipo: $m->tipo,
            archivo_url: $m->archivo_url,
            imagen_portada_url: $m->imagen_portada_url,
            programa_id: $m->programa_id,
            requiere_datos: (bool) $m->requiere_datos,
            descargas: (int) $m->descargas,
            orden: (int) $m->orden,
            activo: (bool) $m->activo,
            created_at: $m->created_at?->toIso8601String(),
            updated_at: $m->updated_at?->toIso8601String(),
            deleted_at: $m->deleted_at?->toIso8601String(),
        );
    }
}
