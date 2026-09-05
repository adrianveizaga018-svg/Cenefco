<?php

namespace App\Application\ListaAprobados\Commands;

final readonly class UpdateListaAprobadosCommand
{
    public function __construct(
        public int   $id,
        public array $changes = [],
    ) {}
}
