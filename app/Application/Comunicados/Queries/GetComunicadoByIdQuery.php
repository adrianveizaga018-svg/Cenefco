<?php

namespace App\Application\Comunicados\Queries;

final readonly class GetComunicadoByIdQuery
{
    public function __construct(public int $id) {}
}
