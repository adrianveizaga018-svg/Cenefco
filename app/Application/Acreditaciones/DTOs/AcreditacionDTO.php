<?php

namespace App\Application\Acreditaciones\DTOs;

final readonly class AcreditacionDTO
{
    public function __construct(
        public int     $id,
        public string  $nombre,
        public string  $entidad_otorgante,
        public ?string $tipo,
        public ?string $descripcion,
        public ?string $logo_url,
        public ?string $logo_alt,
        public ?string $url_verificacion,
        public ?string $fecha_obtencion,
        public ?string $fecha_vencimiento,
        public int     $orden,
        public bool    $activo,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id:                $model->id,
            nombre:            $model->nombre,
            entidad_otorgante: $model->entidad_otorgante,
            tipo:              $model->tipo,
            descripcion:       $model->descripcion,
            logo_url:          $model->logo_url,
            logo_alt:          $model->logo_alt,
            url_verificacion:  $model->url_verificacion,
            fecha_obtencion:   $model->fecha_obtencion?->toDateString(),
            fecha_vencimiento: $model->fecha_vencimiento?->toDateString(),
            orden:             (int) $model->orden,
            activo:            (bool) $model->activo,
            created_at:        $model->created_at?->toIso8601String(),
            updated_at:        $model->updated_at?->toIso8601String(),
        );
    }
}
