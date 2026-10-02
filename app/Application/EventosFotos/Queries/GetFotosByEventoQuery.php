<?php

namespace App\Application\EventosFotos\Queries;

final readonly class GetFotosByEventoQuery
{
    public function __construct(public int $eventoId) {}
}
