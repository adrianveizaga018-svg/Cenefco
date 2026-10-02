<?php

namespace App\Application\Profesiones\Commands;

final readonly class DeleteProfesionCommand
{
    public function __construct(public int $id) {}
}
