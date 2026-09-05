<?php

namespace App\Application\TiposPostgrado\Commands;

final readonly class DeleteTipoPostgradoCommand
{
    public function __construct(public int $idTipopost) {}
}
