<?php

namespace App\Application\CompromisosCobro\Commands;

use App\Domain\CompromisosCobro\Enums\MotivoReprogramacionEnum;

final readonly class ReprogramarCompromisoCobroCommand
{
    public function __construct(
        public int                      $id,
        public string                   $nuevaFecha,
        public ?string                  $nuevaHora,
        public MotivoReprogramacionEnum $motivo,
        public ?string                  $observacion,
        public int                      $registradoPor,
    ) {}
}
