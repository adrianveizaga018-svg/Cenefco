<?php

namespace App\Application\Convenios\DTOs;

final readonly class ConvenioDTO
{
    public function __construct(
        public int $id,
        public string $nombre,
        public ?string $institucion,
        public ?string $tipo,
        public ?string $descripcion,
        public ?string $responsable,
        public ?string $contacto_email,
        public ?string $contacto_telefono,
        public ?string $fecha_inicio,
        public ?string $fecha_fin,
        public ?string $documento_url,
        public ?string $logo_url,
        public string $estado,
        public int $orden,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: $model->id,
            nombre: $model->nombre,
            institucion: $model->institucion,
            tipo: $model->tipo,
            descripcion: $model->descripcion,
            responsable: $model->responsable,
            contacto_email: $model->contacto_email,
            contacto_telefono: $model->contacto_telefono,
            fecha_inicio: $model->fecha_inicio ? (string) $model->fecha_inicio : null,
            fecha_fin: $model->fecha_fin ? (string) $model->fecha_fin : null,
            documento_url: $model->documento_url,
            logo_url: $model->logo_url,
            estado: $model->estado,
            orden: (int) $model->orden,
            created_at: is_string($model->created_at ?? null) ? $model->created_at : $model->created_at?->toIso8601String(),
            updated_at: is_string($model->updated_at ?? null) ? $model->updated_at : $model->updated_at?->toIso8601String(),
        );
    }
}
