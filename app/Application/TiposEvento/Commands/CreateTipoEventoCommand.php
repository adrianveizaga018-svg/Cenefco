<?php

namespace App\Application\TiposEvento\Commands;

final readonly class CreateTipoEventoCommand
{
    public function __construct(
        public string $nombre,
        public ?string $color_hex = null,
        public bool $activo = true,
    ) {}
}
