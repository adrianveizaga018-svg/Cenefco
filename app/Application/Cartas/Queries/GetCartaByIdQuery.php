<?php

namespace App\Application\Cartas\Queries;

final readonly class GetCartaByIdQuery
{
    public function __construct(public int $id) {}
}
