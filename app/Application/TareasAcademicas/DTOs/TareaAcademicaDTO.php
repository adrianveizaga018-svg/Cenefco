<?php

namespace App\Application\TareasAcademicas\DTOs;

class TareaAcademicaDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $programa_id,
        public readonly string $titulo,
        public readonly ?string $descripcion,
        public readonly bool $requiere_archivo,
        public readonly string $estado,
        public readonly ?string $archivo_url,
        public readonly ?int $completado_por_usuario_id,
        public readonly ?string $fecha_completado,
        public readonly ?string $created_at,
        public readonly ?string $updated_at
    ) {}

    public static function fromModel($model): self
    {
        return new self(
            $model->id,
            $model->programa_id,
            $model->titulo,
            $model->descripcion,
            $model->requiere_archivo,
            $model->estado,
            $model->archivo_url ? \Illuminate\Support\Facades\Storage::disk('public')->url($model->archivo_url) : null,
            $model->completado_por_usuario_id,
            $model->fecha_completado?->toIso8601String(),
            $model->created_at?->toIso8601String(),
            $model->updated_at?->toIso8601String()
        );
    }
}
