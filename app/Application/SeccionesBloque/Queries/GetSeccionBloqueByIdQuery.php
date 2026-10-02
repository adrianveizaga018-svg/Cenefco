<?php

namespace App\Application\SeccionesBloque\Queries;

final readonly class GetSeccionBloqueByIdQuery
{
    public function __construct(public int $idSeccionbloque) {}
}
