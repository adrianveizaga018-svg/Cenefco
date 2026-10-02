<?php

namespace App\Application\HistoriaMunicipio\Commands;

final readonly class DeleteHistoriaMunicipioCommand
{
    public function __construct(public int $id) {}
}
