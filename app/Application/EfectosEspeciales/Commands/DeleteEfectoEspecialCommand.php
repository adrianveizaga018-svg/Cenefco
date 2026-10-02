<?php

namespace App\Application\EfectosEspeciales\Commands;

final readonly class DeleteEfectoEspecialCommand
{
    public function __construct(public int $id) {}
}
