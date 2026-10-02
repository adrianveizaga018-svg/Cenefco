<?php

namespace App\Application\CertConfigProgramas\Commands;

final readonly class AprobarSolicitudCommand
{
    public function __construct(public int $solicitud_id) {}
}
