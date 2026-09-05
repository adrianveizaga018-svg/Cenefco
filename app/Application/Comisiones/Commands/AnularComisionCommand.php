<?php

namespace App\Application\Comisiones\Commands;

final readonly class AnularComisionCommand
{
    public function __construct(
        public int     $id,
        public ?string $nota = null,
    ) {}
}
