<?php

namespace App\Application\Comunicados\Commands;

final readonly class DeleteComunicadoCommand
{
    public function __construct(public int $id) {}
}
