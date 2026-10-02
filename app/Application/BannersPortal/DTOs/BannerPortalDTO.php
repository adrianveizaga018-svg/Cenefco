<?php

namespace App\Application\BannersPortal\DTOs;

final readonly class BannerPortalDTO
{
    public function __construct(
        public int     $id,
        public ?string $titulo,
        public ?string $slug,
        public ?string $subtitulo,
        public ?string $imagen_url,
        public ?string $imagen_alt,
        public ?string $imagen_movil_url,
        public ?string $enlace_url,
        public ?string $enlace_texto,
        public ?string $enlace_target,
        public ?string $enlace_url_2,
        public ?string $enlace_texto_2,
        public ?string $posicion,
        public int     $orden,
        public bool    $activo,
        public ?string $fecha_inicio,
        public ?string $fecha_fin,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id:               $model->id,
            titulo:           $model->titulo ?? null,
            slug:             $model->slug ?? null,
            subtitulo:        $model->subtitulo ?? null,
            imagen_url:       $model->imagen_url ?? null,
            imagen_alt:       $model->imagen_alt ?? null,
            imagen_movil_url: $model->imagen_movil_url ?? null,
            enlace_url:       $model->enlace_url ?? null,
            enlace_texto:     $model->enlace_texto ?? null,
            enlace_target:    $model->enlace_target ?? null,
            enlace_url_2:     $model->enlace_url_2 ?? null,
            enlace_texto_2:   $model->enlace_texto_2 ?? null,
            posicion:         $model->posicion ?? null,
            orden:            (int) ($model->orden ?? 0),
            activo:           (bool) ($model->activo ?? true),
            fecha_inicio:     $model->fecha_inicio?->toIso8601String(),
            fecha_fin:        $model->fecha_fin?->toIso8601String(),
            created_at:       $model->created_at?->toIso8601String(),
            updated_at:       $model->updated_at?->toIso8601String(),
        );
    }
}
