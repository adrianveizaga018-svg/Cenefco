<?php

namespace App\Application\UsuariosTipoPrograma\Commands;

final readonly class CreateUsuarioTipoProgramaCommand
{
    public function __construct(
        public int $id_usuariotipoprograma,
        public int $id_us,
        public int $id_us_reg,
        public int $num_usuariotipoprograma,
        public ?int $id_tipoprograma,
        public int $estado,
    ) {}
}
