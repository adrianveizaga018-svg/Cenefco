<?php

namespace App\Application\ListaAprobados\Commands;

final readonly class DeleteListaAprobadosBulkCommand
{
    public function __construct(public array $ids) {}
}
