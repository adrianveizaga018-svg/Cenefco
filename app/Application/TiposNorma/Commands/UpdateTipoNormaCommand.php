<?php

namespace App\Application\TiposNorma\Commands;

final readonly class UpdateTipoNormaCommand
{
    public function __construct(public int $id, public array $data) {}
}
