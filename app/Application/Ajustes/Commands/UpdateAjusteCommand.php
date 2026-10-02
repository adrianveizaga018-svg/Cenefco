<?php

namespace App\Application\Ajustes\Commands;

final readonly class UpdateAjusteCommand
{
    public function __construct(public string $clave, public string $valor) {}
}
