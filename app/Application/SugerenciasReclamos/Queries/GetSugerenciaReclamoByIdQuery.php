<?php

namespace App\Application\SugerenciasReclamos\Queries;

final readonly class GetSugerenciaReclamoByIdQuery
{
    public function __construct(public int $id) {}
}
