<?php

namespace App\Application\UsuariosPrograma\Commands;

final readonly class DeleteUsuarioProgramaCommand
{
    public function __construct(public int $id) {}
}
