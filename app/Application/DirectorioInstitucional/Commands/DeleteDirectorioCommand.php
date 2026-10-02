<?php

namespace App\Application\DirectorioInstitucional\Commands;

final readonly class DeleteDirectorioCommand
{
    public function __construct(public int $id) {}
}
