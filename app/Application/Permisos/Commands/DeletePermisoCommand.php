<?php

namespace App\Application\Permisos\Commands;

final readonly class DeletePermisoCommand
{
    public function __construct(public int $id) {}
}
