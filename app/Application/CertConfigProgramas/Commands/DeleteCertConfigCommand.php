<?php

namespace App\Application\CertConfigProgramas\Commands;

final readonly class DeleteCertConfigCommand
{
    public function __construct(public int $id) {}
}
