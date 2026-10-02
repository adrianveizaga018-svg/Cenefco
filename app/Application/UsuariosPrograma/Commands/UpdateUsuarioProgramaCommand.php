<?php

namespace App\Application\UsuariosPrograma\Commands;

final readonly class UpdateUsuarioProgramaCommand
{
    public function __construct(
        public int $id,
        public ?int $id_programa,
        public ?int $id_tipoprograma,
        public ?int $estado,
    ) {}
}
