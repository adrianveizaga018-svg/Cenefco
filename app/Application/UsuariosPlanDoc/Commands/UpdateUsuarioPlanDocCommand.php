<?php

namespace App\Application\UsuariosPlanDoc\Commands;

final readonly class UpdateUsuarioPlanDocCommand
{
    public function __construct(
        public int $id,
        public ?int $id_plandoc,
        public ?int $estado,
    ) {}
}
