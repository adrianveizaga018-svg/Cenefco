<?php

namespace App\Application\Faqs\Commands;

final readonly class CreateFaqCommand
{
    public function __construct(
        public string  $pregunta,
        public string  $respuesta,
        public ?string $categoria   = null,
        public ?int    $programa_id = null,
        public int     $orden       = 0,
        public bool    $activo      = true,
    ) {}
}
