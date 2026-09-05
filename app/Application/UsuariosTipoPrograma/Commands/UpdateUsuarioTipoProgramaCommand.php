<?php

namespace App\Application\UsuariosTipoPrograma\Commands;

final readonly class UpdateUsuarioTipoProgramaCommand
{
    public function __construct(
        public int $id,
        public ?int $id_tipoprograma,
        public ?int $estado,
    ) {}
}
