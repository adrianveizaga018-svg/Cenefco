<?php

namespace App\Application\Resenas\Queries;

final readonly class GetEstudiantesProgramaQuery
{
    public function __construct(public int $programaId) {}
}
