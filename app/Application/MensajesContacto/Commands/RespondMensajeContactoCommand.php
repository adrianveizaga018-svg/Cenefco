<?php

namespace App\Application\MensajesContacto\Commands;

final readonly class RespondMensajeContactoCommand
{
    public function __construct(
        public int $id,
        public string $respuesta,
        public int $respondido_por,
        public string $estado = 'respondido',
    ) {}
}
