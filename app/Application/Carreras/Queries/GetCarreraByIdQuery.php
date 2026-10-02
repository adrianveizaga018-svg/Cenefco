<?php

namespace App\Application\Carreras\Queries;

final readonly class GetCarreraByIdQuery
{
    public function __construct(public int $id) {}
}
