<?php

namespace App\Application\Universidades\Commands;

final readonly class DeleteUniversidadCommand
{
    public function __construct(public int $id) {}
}
