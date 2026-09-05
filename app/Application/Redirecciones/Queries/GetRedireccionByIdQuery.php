<?php

namespace App\Application\Redirecciones\Queries;

final readonly class GetRedireccionByIdQuery
{
    public function __construct(public int $id) {}
}
