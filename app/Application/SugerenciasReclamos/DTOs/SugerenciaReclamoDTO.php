<?php

namespace App\Application\SugerenciasReclamos\DTOs;

final readonly class SugerenciaReclamoDTO
{
    public function __construct(
        public int $id,
        public ?int $usuario_id,
        public string $asunto,
        public ?string $mensaje,
        public ?string $email_respuesta,
        public ?int $secretaria_destino_id,
        public string $estado,
        public ?string $respuesta,
        public ?int $respondido_por,
        public ?string $respondido_at,
        public ?string $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: (int) $model->id,
            usuario_id: $model->usuario_id ? (int) $model->usuario_id : null,
            asunto: $model->asunto,
            mensaje: $model->mensaje,
            email_respuesta: $model->email_respuesta,
            secretaria_destino_id: $model->secretaria_destino_id ? (int) $model->secretaria_destino_id : null,
            estado: $model->estado,
            respuesta: $model->respuesta,
            respondido_por: $model->respondido_por ? (int) $model->respondido_por : null,
            respondido_at: $model->respondido_at?->toIso8601String(),
            created_at: (is_string($model->created_at ?? null) ? $model->created_at : $model->created_at?->toIso8601String()),
        );
    }
}
