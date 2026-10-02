<?php

namespace App\Application\Usuarios\Queries;

final readonly class GetUsuarioByIdQuery
{
    public function __construct(public int $id) {}
}
