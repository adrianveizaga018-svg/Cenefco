<?php

namespace App\Application\UsuariosTipoPrograma\Queries;

final readonly class GetUsuarioTipoProgramaByIdQuery
{
    public function __construct(public int $id) {}
}
