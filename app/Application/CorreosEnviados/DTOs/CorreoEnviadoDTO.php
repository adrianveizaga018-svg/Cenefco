<?php

namespace App\Application\CorreosEnviados\DTOs;

final readonly class CorreoEnviadoDTO
{
    public function __construct(
        public int     $id,
        public string  $tipo,
        public string  $destinatario,
        public string  $asunto,
        public ?string $referencia_tipo,
        public ?int    $referencia_id,
        public string  $estado,
        public ?string $error,
        public ?int    $enviado_por,
        public ?string $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id:              (int) $model->id,
            tipo:                  $model->tipo,
            destinatario:          $model->destinatario,
            asunto:                $model->asunto,
            referencia_tipo:       $model->referencia_tipo ?? null,
            referencia_id:         $model->referencia_id !== null ? (int) $model->referencia_id : null,
            estado:                $model->estado,
            error:                 $model->error ?? null,
            enviado_por:           $model->enviado_por !== null ? (int) $model->enviado_por : null,
            created_at:            is_string($model->created_at ?? null)
                                        ? $model->created_at
                                        : ($model->created_at?->toIso8601String() ?? null),
        );
    }
}
