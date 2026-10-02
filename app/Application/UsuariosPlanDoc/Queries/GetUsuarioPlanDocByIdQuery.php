<?php

namespace App\Application\UsuariosPlanDoc\Queries;

final readonly class GetUsuarioPlanDocByIdQuery
{
    public function __construct(public int $id) {}
}
