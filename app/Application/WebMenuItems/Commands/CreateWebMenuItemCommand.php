<?php

namespace App\Application\WebMenuItems\Commands;

final readonly class CreateWebMenuItemCommand
{
    public function __construct(
        public int $menuId,
        public ?int $parentId,
        public string $titulo,
        public ?string $url,
        public int $orden,
        public bool $activo,
    ) {}
}
