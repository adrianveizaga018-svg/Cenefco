<?php

namespace App\Application\Redirecciones\Commands;

final readonly class CreateRedireccionCommand
{
    public function __construct(
        public string  $url_origen,
        public string  $url_destino,
        public int     $codigo_http = 301,
        public bool    $activo      = true,
        public ?string $notas       = null,
    ) {}
}
