<?php

namespace App\Application\CampanasPublicidad\Queries;

final readonly class GetCampanaByIdQuery
{
    public function __construct(public int $id) {}
}
