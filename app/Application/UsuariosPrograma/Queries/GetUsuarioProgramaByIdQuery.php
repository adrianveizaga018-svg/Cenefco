<?php

namespace App\Application\UsuariosPrograma\Queries;

final readonly class GetUsuarioProgramaByIdQuery
{
    public function __construct(public int $id) {}
}
