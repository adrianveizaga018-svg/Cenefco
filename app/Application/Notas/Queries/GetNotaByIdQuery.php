<?php

namespace App\Application\Notas\Queries;

final readonly class GetNotaByIdQuery
{
    public function __construct(public int $id) {}
}
