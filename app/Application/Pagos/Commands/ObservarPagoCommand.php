<?php

namespace App\Application\Pagos\Commands;

final readonly class ObservarPagoCommand
{
    public function __construct(
        public int $id,
        public string $nota,
        public int $idUsReg,
    ) {}
}
