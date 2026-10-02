<?php

namespace App\Application\EventosFotos\Queries;

final readonly class GetEventoFotoByIdQuery
{
    public function __construct(public int $id) {}
}
