<?php

namespace App\Application\CartaModelos\Commands;

final readonly class DeleteCartaModeloCommand
{
    public function __construct(public int $id) {}
}
