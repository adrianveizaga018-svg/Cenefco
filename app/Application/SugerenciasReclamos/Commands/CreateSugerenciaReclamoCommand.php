<?php

namespace App\Application\SugerenciasReclamos\Commands;

final readonly class CreateSugerenciaReclamoCommand
{
    public function __construct(
        public string $asunto,
        public string $mensaje,
        public ?string $email_respuesta = null,
        public ?int $secretaria_destino_id = null,
        public ?int $usuario_id = null,
    ) {}
}
