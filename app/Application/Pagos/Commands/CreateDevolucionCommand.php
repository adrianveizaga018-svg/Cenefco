<?php

namespace App\Application\Pagos\Commands;

final readonly class CreateDevolucionCommand
{
    public function __construct(
        public int     $idIns,
        public float   $monto,
        public string  $motivo,
        public ?string $documentoUrl = null,
    ) {}
}
