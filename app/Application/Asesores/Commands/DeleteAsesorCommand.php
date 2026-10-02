<?php

namespace App\Application\Asesores\Commands;

final readonly class DeleteAsesorCommand
{
    public function __construct(public int $id) {}
}
