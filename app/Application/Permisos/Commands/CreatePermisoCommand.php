<?php

namespace App\Application\Permisos\Commands;

final readonly class CreatePermisoCommand
{
    public function __construct(
        public string $codigo,
        public ?string $descripcion,
        public ?string $modulo,
    ) {}
}
