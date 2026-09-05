<?php

namespace App\Application\Cartas\Commands;

final readonly class DeleteCartaCommand
{
    public function __construct(public int $id) {}
}
