<?php

namespace App\Application\Autoridades\Queries;

final readonly class GetAutoridadByIdQuery
{
    public function __construct(public int $id) {}
}
