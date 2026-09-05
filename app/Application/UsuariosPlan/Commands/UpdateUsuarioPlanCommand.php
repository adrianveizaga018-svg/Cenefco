<?php

namespace App\Application\UsuariosPlan\Commands;

final readonly class UpdateUsuarioPlanCommand
{
    public function __construct(
        public int $id,
        public ?int $id_plan,
        public ?int $estado,
    ) {}
}
