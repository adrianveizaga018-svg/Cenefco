<?php

namespace App\Application\WebMenus\Commands;

final readonly class UpdateWebMenuCommand
{
    public function __construct(
        public int $id,
        public ?string $nombre,
        public ?string $descripcion,
        public ?bool $activo,
    ) {}
}
