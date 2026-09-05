<?php

namespace App\Application\AjustesSueldo\Commands;

final readonly class DeleteAjusteSueldoCommand
{
    public function __construct(public int $id) {}
}
