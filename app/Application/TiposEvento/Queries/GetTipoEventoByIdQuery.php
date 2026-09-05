<?php

namespace App\Application\TiposEvento\Queries;

final readonly class GetTipoEventoByIdQuery
{
    public function __construct(public int $id) {}
}
