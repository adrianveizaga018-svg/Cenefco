<?php

namespace App\Application\SugerenciasReclamos\Commands;

final readonly class DeleteSugerenciaReclamoCommand
{
    public function __construct(public int $id) {}
}
