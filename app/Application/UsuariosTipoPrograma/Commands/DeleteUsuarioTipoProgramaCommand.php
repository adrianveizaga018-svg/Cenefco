<?php

namespace App\Application\UsuariosTipoPrograma\Commands;

final readonly class DeleteUsuarioTipoProgramaCommand
{
    public function __construct(public int $id) {}
}
