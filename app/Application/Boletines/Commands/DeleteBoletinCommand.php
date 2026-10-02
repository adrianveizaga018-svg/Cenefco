<?php

namespace App\Application\Boletines\Commands;

final readonly class DeleteBoletinCommand
{
    public function __construct(public int $id) {}
}
