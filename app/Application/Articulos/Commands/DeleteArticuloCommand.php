<?php

namespace App\Application\Articulos\Commands;

final readonly class DeleteArticuloCommand
{
    public function __construct(public int $id) {}
}
