<?php

namespace App\Application\Aliados\Queries;

final readonly class GetAliadoByIdQuery
{
    public function __construct(public int $id) {}
}
