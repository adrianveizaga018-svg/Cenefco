<?php

namespace App\Application\SeccionesBloque\Commands;

final readonly class DeleteSeccionBloqueCommand
{
    public function __construct(public int $idSeccionbloque) {}
}
