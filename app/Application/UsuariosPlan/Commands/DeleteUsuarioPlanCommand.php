<?php

namespace App\Application\UsuariosPlan\Commands;

final readonly class DeleteUsuarioPlanCommand
{
    public function __construct(public int $id) {}
}
