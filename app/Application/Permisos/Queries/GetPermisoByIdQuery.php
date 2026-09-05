<?php

namespace App\Application\Permisos\Queries;

final readonly class GetPermisoByIdQuery
{
    public function __construct(public int $id) {}
}
