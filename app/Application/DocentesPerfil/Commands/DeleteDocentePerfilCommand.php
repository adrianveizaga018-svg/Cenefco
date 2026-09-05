<?php

namespace App\Application\DocentesPerfil\Commands;

final readonly class DeleteDocentePerfilCommand
{
    public function __construct(public int $id) {}
}
