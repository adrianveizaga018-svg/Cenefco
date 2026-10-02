<?php

namespace App\Application\TiposNorma\Commands;

final readonly class DeleteTipoNormaCommand
{
    public function __construct(public int $id) {}
}
