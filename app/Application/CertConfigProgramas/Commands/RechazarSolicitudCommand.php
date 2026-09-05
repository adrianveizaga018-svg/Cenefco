<?php

namespace App\Application\CertConfigProgramas\Commands;

final readonly class RechazarSolicitudCommand
{
    public function __construct(
        public int $solicitud_id,
        public string $nota_admin,
    ) {}
}
