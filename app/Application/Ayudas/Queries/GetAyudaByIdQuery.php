<?php

namespace App\Application\Ayudas\Queries;

final readonly class GetAyudaByIdQuery
{
    public function __construct(public int $id) {}
}
