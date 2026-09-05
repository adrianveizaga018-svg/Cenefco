<?php

namespace App\Application\TiposBanco\Queries;

final readonly class GetTipoBancoByIdQuery
{
    public function __construct(public int $id) {}
}
