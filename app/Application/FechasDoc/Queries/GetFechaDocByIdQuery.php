<?php

namespace App\Application\FechasDoc\Queries;

final readonly class GetFechaDocByIdQuery
{
    public function __construct(public int $id) {}
}
