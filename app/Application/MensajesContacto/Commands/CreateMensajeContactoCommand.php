<?php

namespace App\Application\MensajesContacto\Commands;

final readonly class CreateMensajeContactoCommand
{
    public function __construct(
        public string $nombre_remitente,
        public string $email_remitente,
        public string $asunto,
        public string $mensaje,
        public ?string $telefono_remitente = null,
        public ?int $secretaria_destino_id = null,
        public ?string $ip_origen = null,
    ) {}
}
