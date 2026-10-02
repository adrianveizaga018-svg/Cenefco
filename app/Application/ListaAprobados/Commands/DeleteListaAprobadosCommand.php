<?php

namespace App\Application\ListaAprobados\Commands;

final readonly class DeleteListaAprobadosCommand
{
    public function __construct(public int $id) {}
}
