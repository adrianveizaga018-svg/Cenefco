<?php

namespace App\Application\UsuariosPlan\Commands;

final readonly class CreateUsuarioPlanCommand
{
    public function __construct(
        public int $id_usuarioplan,
        public int $id_us,
        public int $id_us_reg,
        public int $num_usuarioplan,
        public ?int $id_plan,
        public int $estado,
    ) {}
}
