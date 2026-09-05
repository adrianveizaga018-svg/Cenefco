<?php

namespace App\Application\Testimonios\Commands;

final readonly class UpdateTestimonioCommand
{
    public function __construct(
        public int     $id,
        public ?string $nombre       = null,
        public ?string $testimonio   = null,
        public ?string $cargo        = null,
        public ?string $empresa      = null,
        public ?int    $calificacion = null,
        public ?string $foto_url     = null,
        public ?string $foto_alt     = null,
        public ?int    $programa_id  = null,
        public ?bool   $destacado    = null,
        public ?int    $orden        = null,
        public ?string $estado       = null,
    ) {}
}
