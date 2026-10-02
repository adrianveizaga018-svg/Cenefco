<?php

namespace App\Application\CategoriasCampo\DTOs;

final readonly class CategoriaCampoDTO
{
    public function __construct(
        public int $id,
        public int $categoria_id,
        public string $nombre_campo,
        public string $etiqueta,
        public string $tipo_campo,
        public bool $requerido,
        public int $orden,
        public int $paso,
        public bool $activo,
        public ?string $ayuda,
        public mixed $opciones,
        public mixed $validacion,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: $model->id,
            categoria_id: (int) $model->categoria_id,
            nombre_campo: $model->nombre_campo,
            etiqueta: $model->etiqueta,
            tipo_campo: $model->tipo_campo,
            requerido: (bool) $model->requerido,
            orden: (int) $model->orden,
            paso: (int) $model->paso,
            activo: (bool) $model->activo,
            ayuda: $model->ayuda,
            opciones: $model->opciones,
            validacion: $model->validacion,
            created_at: is_string($model->created_at ?? null) ? $model->created_at : $model->created_at?->toIso8601String(),
            updated_at: is_string($model->updated_at ?? null) ? $model->updated_at : $model->updated_at?->toIso8601String(),
        );
    }
}
