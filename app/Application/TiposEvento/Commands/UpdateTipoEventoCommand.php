<?php

namespace App\Application\TiposEvento\Commands;

final readonly class UpdateTipoEventoCommand
{
    public function __construct(public int $id, public array $data) {}
}
