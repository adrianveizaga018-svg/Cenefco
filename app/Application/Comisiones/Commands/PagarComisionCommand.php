<?php

namespace App\Application\Comisiones\Commands;

final readonly class PagarComisionCommand
{
    public function __construct(
        public int     $id,
        public int     $pagadoPor,
        public ?string $comprobantePagoUrl,
    ) {}
}
