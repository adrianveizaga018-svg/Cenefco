<?php

namespace App\Application\Redirecciones\Commands;

final readonly class UpdateRedireccionCommand
{
    public function __construct(
        public int     $id,
        public ?string $url_origen  = null,
        public ?string $url_destino = null,
        public ?int    $codigo_http = null,
        public ?bool   $activo      = null,
        public ?string $notas       = null,
    ) {}
}
