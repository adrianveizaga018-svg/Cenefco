<?php

namespace App\Application\HitosInstitucionales\Queries;

final readonly class GetHitoInstitucionalByIdQuery
{
    public function __construct(public int $id) {}
}
