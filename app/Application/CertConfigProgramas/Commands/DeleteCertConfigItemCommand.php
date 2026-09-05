<?php

namespace App\Application\CertConfigProgramas\Commands;

final readonly class DeleteCertConfigItemCommand
{
    public function __construct(public int $id) {}
}
