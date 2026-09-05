<?php

namespace App\Application\Horarios\Commands;

final readonly class DeleteHorarioCommand
{
    public function __construct(public int $id) {}
}
