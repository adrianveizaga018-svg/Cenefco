<?php

namespace App\Application\MediosPago\Commands;

final readonly class CreateMedioPagoCommand
{
    public function __construct(
        public string $nombre,
        public int    $orden,
        public bool   $activo,
    ) {}
}
