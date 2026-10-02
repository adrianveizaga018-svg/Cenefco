<?php

namespace App\Application\SugerenciasReclamos\Commands;

final readonly class RespondSugerenciaReclamoCommand
{
    public function __construct(
        public int $id,
        public string $respuesta,
        public int $respondido_por,
        public string $estado = 'resuelto',
    ) {}
}
