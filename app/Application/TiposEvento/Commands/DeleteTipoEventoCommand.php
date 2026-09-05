<?php

namespace App\Application\TiposEvento\Commands;

final readonly class DeleteTipoEventoCommand
{
    public function __construct(public int $id) {}
}
