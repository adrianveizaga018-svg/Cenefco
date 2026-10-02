<?php

namespace App\Application\Secretarias\DTOs;

final readonly class SecretariaDTO
{
    public function __construct(
        public int $id,
        public string $nombre,
        public ?string $sigla,
        public ?string $atribuciones,
        public ?string $direccion_fisica,
        public ?string $telefono,
        public ?string $email,
        public ?string $horario_atencion,
        public ?string $foto_titular_url,
        public int $orden_organigrama,
        public bool $activa,
        public string $slug,
        public ?string $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: $model->id,
            nombre: $model->nombre,
            sigla: $model->sigla,
            atribuciones: $model->atribuciones,
            direccion_fisica: $model->direccion_fisica,
            telefono: $model->telefono,
            email: $model->email,
            horario_atencion: $model->horario_atencion,
            foto_titular_url: $model->foto_titular_url,
            orden_organigrama: (int) $model->orden_organigrama,
            activa: (bool) $model->activa,
            slug: $model->slug,
            created_at: (is_string($model->created_at ?? null) ? $model->created_at : $model->created_at?->toIso8601String()),
        );
    }
}
