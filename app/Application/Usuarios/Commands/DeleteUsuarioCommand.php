<?php

namespace App\Application\Usuarios\Commands;

final readonly class DeleteUsuarioCommand
{
    public function __construct(public int $id) {}
}
