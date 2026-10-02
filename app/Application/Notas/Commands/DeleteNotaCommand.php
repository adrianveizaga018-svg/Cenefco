<?php

namespace App\Application\Notas\Commands;

final readonly class DeleteNotaCommand
{
    public function __construct(public int $id) {}
}
