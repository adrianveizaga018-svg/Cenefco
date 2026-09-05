<?php

namespace App\Application\DocentesPerfil\Queries;

final readonly class GetDocentePerfilBySlugQuery
{
    public function __construct(public string $slug) {}
}
