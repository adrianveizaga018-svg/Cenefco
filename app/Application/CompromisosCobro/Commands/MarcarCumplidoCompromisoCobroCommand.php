<?php

namespace App\Application\CompromisosCobro\Commands;

final readonly class MarcarCumplidoCompromisoCobroCommand
{
    public function __construct(
        public int     $id,
        public int     $registradoPor,
        public ?string $observacion = null,
    ) {}
}
