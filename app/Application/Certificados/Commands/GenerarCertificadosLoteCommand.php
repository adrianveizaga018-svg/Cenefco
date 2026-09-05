<?php

namespace App\Application\Certificados\Commands;

final readonly class GenerarCertificadosLoteCommand
{
    public function __construct(
        public int $imparteId,
        public int $plantillaId,
        public ?array $usuarioIds = null,
    ) {}
}
