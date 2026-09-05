<?php

namespace App\Application\Autoridades\DTOs;

final readonly class AutoridadDTO
{
    public function __construct(
        public int $id,
        public ?int $secretaria_id,
        public string $nombre,
        public string $apellido,
        public string $cargo,
        public string $tipo,
        public ?string $perfil_profesional,
        public ?string $email_institucional,
        public ?string $foto_url,
        public int $orden,
        public bool $activo,
        public bool $publicado_web,
        public ?string $fecha_inicio_cargo,
        public ?string $fecha_fin_cargo,
        public ?string $slug,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: $model->id,
            secretaria_id: $model->secretaria_id ? (int) $model->secretaria_id : null,
            nombre: $model->nombre,
            apellido: $model->apellido,
            cargo: $model->cargo,
            tipo: $model->tipo,
            perfil_profesional: $model->perfil_profesional,
            email_institucional: $model->email_institucional,
            foto_url: $model->foto_url
                ? (str_starts_with($model->foto_url, '/storage/') || str_starts_with($model->foto_url, 'http')
                    ? $model->foto_url
                    : '/storage/'.$model->foto_url)
                : null,
            orden: (int) $model->orden,
            activo: (bool) $model->activo,
            publicado_web: (bool) $model->publicado_web,
            fecha_inicio_cargo: $model->fecha_inicio_cargo?->toDateString(),
            fecha_fin_cargo: $model->fecha_fin_cargo?->toDateString(),
            slug: $model->slug,
        );
    }
}
