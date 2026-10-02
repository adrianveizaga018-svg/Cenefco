<?php

namespace App\Application\Testimonios\Commands;

final readonly class CreateTestimonioCommand
{
    public function __construct(
        public string  $nombre,
        public string  $testimonio,
        public ?string $cargo        = null,
        public ?string $empresa      = null,
        public int     $calificacion = 5,
        public ?string $foto_url     = null,
        public ?string $foto_alt     = null,
        public ?int    $programa_id  = null,
        public bool    $destacado    = false,
        public int     $orden        = 0,
        public string  $estado       = 'publicado',
    ) {}
}
