<?php

namespace App\Application\Materias\Commands;

final readonly class DeleteMateriaCommand
{
    public function __construct(public int $id) {}
}
