<?php

namespace App\Application\CompromisosCobro\Commands;

final readonly class CreateCompromisoCobroCommand
{
    public function __construct(
        public int     $idIns,
        public int     $idUs,
        public int     $idImp,
        public string  $fechaCompromiso,
        public ?string $horaCompromiso,
        public ?float  $montoComprometido,
        public ?string $observacion,
        public int     $registradoPor,
    ) {}
}
