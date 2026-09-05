<?php

namespace App\Application\ListaAprobados\Queries;

final readonly class GetListaAprobadoByIdQuery
{
    public function __construct(public int $id) {}
}
