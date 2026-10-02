<?php

namespace App\Application\CifrasInstitucionales\Queries;

final readonly class GetCifraInstitucionalByIdQuery
{
    public function __construct(public int $id) {}
}
