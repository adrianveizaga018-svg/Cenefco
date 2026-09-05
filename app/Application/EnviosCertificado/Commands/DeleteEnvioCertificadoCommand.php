<?php

namespace App\Application\EnviosCertificado\Commands;

final readonly class DeleteEnvioCertificadoCommand
{
    public function __construct(public int $id) {}
}
