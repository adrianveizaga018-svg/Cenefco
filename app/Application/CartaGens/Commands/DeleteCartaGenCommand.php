<?php

namespace App\Application\CartaGens\Commands;

final readonly class DeleteCartaGenCommand
{
    public function __construct(public int $id) {}
}
