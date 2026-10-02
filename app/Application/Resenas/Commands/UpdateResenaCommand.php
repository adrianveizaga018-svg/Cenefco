<?php

namespace App\Application\Resenas\Commands;

final readonly class UpdateResenaCommand
{
    public function __construct(
        public int     $id,
        public ?string $estado        = null,
        public ?bool   $verificado    = null,
        public ?bool   $destacada     = null,
        public ?string $motivo_rechazo = null,
        public ?string $foto_url      = null,
        public ?string $nombre        = null,
        public ?string $cargo_actual  = null,
        public ?int    $calificacion  = null,
        public ?string $titulo_resena = null,
        public ?string $resena        = null,
        public ?int    $usuario_id    = null,
    ) {}
}
