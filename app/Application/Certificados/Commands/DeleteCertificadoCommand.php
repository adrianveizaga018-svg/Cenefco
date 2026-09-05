<?php

namespace App\Application\Certificados\Commands;

final readonly class DeleteCertificadoCommand
{
    public function __construct(public int $id) {}
}
