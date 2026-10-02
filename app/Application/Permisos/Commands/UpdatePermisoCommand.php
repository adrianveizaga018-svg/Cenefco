<?php

namespace App\Application\Permisos\Commands;

final readonly class UpdatePermisoCommand
{
    public function __construct(
        public int $id,
        public array $data,
    ) {}
}
