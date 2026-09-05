<?php

namespace App\Application\CertPlantillaCampos\Queries;

final readonly class GetCertPlantillaCampoByIdQuery
{
    public function __construct(public int $id) {}
}
