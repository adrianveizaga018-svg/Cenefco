<?php

namespace App\Application\Tesis\Queries;

final readonly class GetTesisByIdQuery
{
    public function __construct(public int $id) {}
}
