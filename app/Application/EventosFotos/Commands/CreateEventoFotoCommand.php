<?php

namespace App\Application\EventosFotos\Commands;

final readonly class CreateEventoFotoCommand
{
    public function __construct(
        public int $evento_id,
        public string $archivo_url,
        public ?string $titulo = null,
        public int $orden = 0,
    ) {}
}
