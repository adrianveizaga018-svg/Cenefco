<?php

namespace App\Application\EventosFotos\Commands;

final readonly class DeleteEventoFotoCommand
{
    public function __construct(public int $id) {}
}
