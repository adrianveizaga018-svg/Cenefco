<?php

namespace App\Application\Fotos\Queries;

final readonly class GetFotoByIdQuery
{
    public function __construct(public int $id) {}
}
