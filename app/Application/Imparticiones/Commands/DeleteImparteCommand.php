<?php

namespace App\Application\Imparticiones\Commands;

final readonly class DeleteImparteCommand
{
    public function __construct(public int $id) {}
}
