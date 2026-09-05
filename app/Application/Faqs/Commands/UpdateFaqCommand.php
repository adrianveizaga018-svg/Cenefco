<?php

namespace App\Application\Faqs\Commands;

final readonly class UpdateFaqCommand
{
    public function __construct(
        public int     $id,
        public ?string $pregunta    = null,
        public ?string $respuesta   = null,
        public ?string $categoria   = null,
        public ?int    $programa_id = null,
        public ?int    $orden       = null,
        public ?bool   $activo      = null,
    ) {}
}
