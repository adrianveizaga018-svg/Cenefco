<?php

namespace App\Application\CertConfigProgramas\Commands;

final readonly class ToggleCertConfigCommand
{
    public function __construct(public int $id) {}
}
