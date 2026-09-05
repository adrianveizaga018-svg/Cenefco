<?php

namespace App\Application\Formularios\Commands;

final readonly class DeleteFormularioCommand
{
    public function __construct(public int $id) {}
}
