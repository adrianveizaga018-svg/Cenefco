<?php

namespace App\Application\UsuariosPrograma\Commands;

final readonly class CreateUsuarioProgramaCommand
{
    public function __construct(
        public int $id_usuarioprograma,
        public int $id_us,
        public int $id_us_reg,
        public int $num_usuarioprograma,
        public ?int $id_programa,
        public ?int $id_tipoprograma,
        public int $estado,
    ) {}
}
