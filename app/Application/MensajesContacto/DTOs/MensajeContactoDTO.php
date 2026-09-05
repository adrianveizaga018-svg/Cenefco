<?php

namespace App\Application\MensajesContacto\DTOs;

final readonly class MensajeContactoDTO
{
    public function __construct(
        public int $id,
        public ?int $secretaria_destino_id,
        public string $nombre_remitente,
        public string $email_remitente,
        public ?string $telefono_remitente,
        public string $asunto,
        public string $mensaje,
        public string $estado,
        public ?string $respuesta,
        public ?int $respondido_por,
        public ?string $respondido_at,
        public ?string $ip_origen,
        public ?string $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: (int) $model->id,
            secretaria_destino_id: $model->secretaria_destino_id ? (int) $model->secretaria_destino_id : null,
            nombre_remitente: $model->nombre_remitente,
            email_remitente: $model->email_remitente,
            telefono_remitente: $model->telefono_remitente,
            asunto: $model->asunto,
            mensaje: $model->mensaje,
            estado: $model->estado,
            respuesta: $model->respuesta,
            respondido_por: $model->respondido_por ? (int) $model->respondido_por : null,
            respondido_at: $model->respondido_at?->toIso8601String(),
            ip_origen: $model->ip_origen,
            created_at: $model->created_at?->toIso8601String(),
        );
    }
}
