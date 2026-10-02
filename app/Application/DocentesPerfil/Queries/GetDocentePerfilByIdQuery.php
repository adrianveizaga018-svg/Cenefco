<?php

namespace App\Application\DocentesPerfil\Queries;

final readonly class GetDocentePerfilByIdQuery
{
    public function __construct(public int $id) {}
}
