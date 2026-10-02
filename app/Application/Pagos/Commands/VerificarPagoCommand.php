<?php

namespace App\Application\Pagos\Commands;

final readonly class VerificarPagoCommand
{
    public function __construct(
        public int $id,
        public int $idUsReg,
    ) {}
}
