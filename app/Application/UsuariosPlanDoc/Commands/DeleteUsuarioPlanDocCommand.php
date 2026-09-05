<?php

namespace App\Application\UsuariosPlanDoc\Commands;

final readonly class DeleteUsuarioPlanDocCommand
{
    public function __construct(public int $id) {}
}
