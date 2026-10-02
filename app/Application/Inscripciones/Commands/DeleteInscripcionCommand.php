<?php

namespace App\Application\Inscripciones\Commands;

final readonly class DeleteInscripcionCommand
{
    public function __construct(public int $id) {}
}
