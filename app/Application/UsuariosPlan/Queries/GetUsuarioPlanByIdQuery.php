<?php

namespace App\Application\UsuariosPlan\Queries;

final readonly class GetUsuarioPlanByIdQuery
{
    public function __construct(public int $id) {}
}
