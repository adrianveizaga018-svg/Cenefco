<?php

namespace App\Application\CertPlantillaCampos\Commands;

final readonly class DeleteCertPlantillaCampoCommand
{
    public function __construct(public int $id) {}
}
