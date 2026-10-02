<?php

namespace App\Application\CifrasInstitucionales\Commands;

final readonly class DeleteCifraInstitucionalCommand
{
    public function __construct(public int $id) {}
}
