<?php

namespace App\Application\Pagos\Queries;

final readonly class GetDevolucionesQuery
{
    public function __construct(public int $idIns) {}
}
