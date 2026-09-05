<?php

namespace App\Application\CitasAsesoria\Queries;

final readonly class GetCitaAsesoriaByIdQuery
{
    public function __construct(public int $id) {}
}
