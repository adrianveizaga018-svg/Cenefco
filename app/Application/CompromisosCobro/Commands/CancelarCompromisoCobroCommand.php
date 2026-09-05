<?php

namespace App\Application\CompromisosCobro\Commands;

final readonly class CancelarCompromisoCobroCommand
{
    public function __construct(
        public int     $id,
        public int     $registradoPor,
        public ?string $observacion = null,
    ) {}
}
