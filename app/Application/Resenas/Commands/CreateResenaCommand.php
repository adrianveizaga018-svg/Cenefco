<?php

namespace App\Application\Resenas\Commands;

final readonly class CreateResenaCommand
{
    public function __construct(
        public int     $programa_id,
        public ?int    $usuario_id,
        public string  $nombre,
        public ?string $cargo_actual,
        public ?string $foto_url,
        public int     $calificacion,
        public ?string $titulo_resena,
        public string  $resena,
        public bool    $destacada,
    ) {}
}
