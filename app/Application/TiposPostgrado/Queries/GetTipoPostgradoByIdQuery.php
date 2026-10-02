<?php

namespace App\Application\TiposPostgrado\Queries;

final readonly class GetTipoPostgradoByIdQuery
{
    public function __construct(public int $id) {}
}
