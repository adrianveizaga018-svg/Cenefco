<?php

namespace App\Application\MediosPago\Commands;

final readonly class UpdateMedioPagoCommand
{
    public function __construct(
        public int     $id,
        public ?string $nombre,
        public ?int    $orden,
        public ?bool   $activo,
    ) {}
}
