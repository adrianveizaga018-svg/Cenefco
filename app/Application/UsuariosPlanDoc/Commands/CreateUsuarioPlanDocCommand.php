<?php

namespace App\Application\UsuariosPlanDoc\Commands;

final readonly class CreateUsuarioPlanDocCommand
{
    public function __construct(
        public int $id_usuarioplandoc,
        public int $id_us,
        public int $id_us_reg,
        public int $num_usuarioplandoc,
        public ?int $id_plandoc,
        public int $estado,
    ) {}
}
