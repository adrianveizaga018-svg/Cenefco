<?php

namespace App\Application\Boletines\Queries;

final readonly class GetBoletinByIdQuery
{
    public function __construct(public int $id) {}
}
