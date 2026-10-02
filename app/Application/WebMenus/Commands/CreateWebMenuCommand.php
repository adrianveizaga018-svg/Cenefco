<?php

namespace App\Application\WebMenus\Commands;

final readonly class CreateWebMenuCommand
{
    public function __construct(
        public string $nombre,
        public ?string $descripcion,
        public bool $activo,
    ) {}
}
