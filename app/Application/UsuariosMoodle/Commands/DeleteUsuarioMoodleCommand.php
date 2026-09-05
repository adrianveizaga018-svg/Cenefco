<?php

namespace App\Application\UsuariosMoodle\Commands;

final readonly class DeleteUsuarioMoodleCommand
{
    public function __construct(public int $id) {}
}
