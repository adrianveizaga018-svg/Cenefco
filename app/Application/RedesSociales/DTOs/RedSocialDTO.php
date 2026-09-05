<?php

namespace App\Application\RedesSociales\DTOs;

final readonly class RedSocialDTO
{
    public function __construct(
        public int     $id,
        public string  $red,
        public ?string $nombre_display,
        public string  $url,
        public ?string $icono_clase,
        public ?string $pixel_id,
        public bool    $mostrar_footer,
        public bool    $mostrar_header,
        public int     $orden,
        public bool    $activo,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id:             $model->id,
            red:            $model->red,
            nombre_display: $model->nombre_display ?? null,
            url:            $model->url,
            icono_clase:    $model->icono_clase ?? null,
            pixel_id:       $model->pixel_id ?? null,
            mostrar_footer: (bool) ($model->mostrar_footer ?? false),
            mostrar_header: (bool) ($model->mostrar_header ?? false),
            orden:          (int) ($model->orden ?? 0),
            activo:         (bool) ($model->activo ?? true),
        );
    }
}
