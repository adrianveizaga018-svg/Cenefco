<?php

namespace App\Application\CitasAsesoria\Commands;

final readonly class DeleteCitaAsesoriaCommand
{
    public function __construct(public int $id) {}
}
