<?php

namespace App\Application\EfectosEspeciales\Commands;

final readonly class UpdateEfectoEspecialCommand
{
    public function __construct(public int $id, public array $data) {}
}
