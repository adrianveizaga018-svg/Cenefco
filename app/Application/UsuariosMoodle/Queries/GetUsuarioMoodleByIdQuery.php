<?php

namespace App\Application\UsuariosMoodle\Queries;

final readonly class GetUsuarioMoodleByIdQuery
{
    public function __construct(public int $id) {}
}
