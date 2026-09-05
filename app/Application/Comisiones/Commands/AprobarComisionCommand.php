<?php

namespace App\Application\Comisiones\Commands;

final readonly class AprobarComisionCommand
{
    public function __construct(
        public int $id,
        public int $aprobadoPor,
    ) {}
}
