<?php

namespace App\Application\EventosFotos\Commands;

final readonly class UpdateEventoFotoCommand
{
    public function __construct(
        public int     $id,
        public ?string $archivo_url = null,
        public ?string $titulo      = null,
        public ?int    $orden       = null,
    ) {}
}
