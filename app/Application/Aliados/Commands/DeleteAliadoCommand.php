<?php

namespace App\Application\Aliados\Commands;

final readonly class DeleteAliadoCommand
{
    public function __construct(public int $id) {}
}
