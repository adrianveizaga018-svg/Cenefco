<?php

namespace App\Application\TiposPrograma\Queries;

final readonly class GetTipoProgramaByIdQuery
{
    public function __construct(public int $id) {}
}
