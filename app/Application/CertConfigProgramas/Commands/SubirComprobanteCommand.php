<?php

namespace App\Application\CertConfigProgramas\Commands;

final readonly class SubirComprobanteCommand
{
    public function __construct(
        public int $solicitud_id,
        public string $comprobante_url,
    ) {}
}
