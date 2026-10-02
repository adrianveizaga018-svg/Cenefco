<?php

namespace App\Application\HitosInstitucionales\Commands;

final readonly class DeleteHitoInstitucionalCommand
{
    public function __construct(public int $id) {}
}
