<?php

namespace App\Application\UsuariosAcademicos\Commands;

final readonly class DeleteUsuarioAcademicoCommand
{
    public function __construct(public int $id) {}
}
