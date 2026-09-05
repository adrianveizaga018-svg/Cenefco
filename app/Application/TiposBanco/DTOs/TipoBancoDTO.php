<?php

namespace App\Application\TiposBanco\DTOs;

final readonly class TipoBancoDTO
{
    public function __construct(
        public int     $id,
        public string  $nombre,
        public bool    $activo,
        public int     $orden,
        public ?string $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id:         (int) $model->id,
            nombre:     $model->nombre,
            activo:     (bool) $model->activo,
            orden:      (int) ($model->orden ?? 0),
            created_at: is_string($model->created_at ?? null)
                            ? $model->created_at
                            : $model->created_at?->toIso8601String(),
        );
    }
}
