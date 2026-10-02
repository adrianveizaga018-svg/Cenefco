<?php

namespace App\Application\UsuariosAcademicos\Queries;

final readonly class GetUsuarioAcademicoByIdQuery
{
    public function __construct(public int $id) {}
}
