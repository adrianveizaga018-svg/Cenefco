<?php

namespace App\Application\CertPlantillas\Commands;

final readonly class DeleteCertPlantillaCommand
{
    public function __construct(public int $id) {}
}
