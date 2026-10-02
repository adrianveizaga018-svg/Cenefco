<?php

namespace App\Application\InscripcionesDiplomado\Commands;

final readonly class DeleteInscripcionDiplomadoCommand
{
    public function __construct(public int $id) {}
}
