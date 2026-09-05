<?php

namespace App\Application\Comunicados\DTOs;

final readonly class ComunicadoDTO
{
    public function __construct(
        public int $id,
        public string $titulo,
        public ?string $slug,
        public ?string $resumen,
        public ?string $cuerpo,
        public ?string $imagen_url,
        public ?string $archivo_url,
        public string $estado,
        public bool $destacado,
        public int $vistas,
        public ?string $fecha_publicacion,
        public ?string $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: (int) $model->id,
            titulo: $model->titulo,
            slug: $model->slug,
            resumen: $model->resumen,
            cuerpo: $model->cuerpo,
            imagen_url: $model->imagen_url,
            archivo_url: $model->archivo_url,
            estado: $model->estado,
            destacado: (bool) $model->destacado,
            vistas: (int) $model->vistas,
            fecha_publicacion: $model->fecha_publicacion?->toIso8601String(),
            created_at: (is_string($model->created_at ?? null) ? $model->created_at : $model->created_at?->toIso8601String()),
        );
    }
}
