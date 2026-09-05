<?php

namespace App\Application\Pagos\Commands;

final readonly class UpdateFechaPagoCommand
{
    public function __construct(
        public int   $id,
        public array $data,
    ) {}
}
