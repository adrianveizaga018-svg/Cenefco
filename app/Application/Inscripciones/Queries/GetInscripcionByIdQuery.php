<?php

namespace App\Application\Inscripciones\Queries;

final readonly class GetInscripcionByIdQuery
{
    public function __construct(public int $id) {}
}
