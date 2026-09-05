<?php

namespace App\Application\PreguntasFrecuentes\Queries;

final readonly class GetPreguntaFrecuenteByIdQuery
{
    public function __construct(public int $id) {}
}
