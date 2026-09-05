<?php

namespace App\Application\CertPlantillas\Queries;

final readonly class GetCertPlantillaByIdQuery
{
    public function __construct(public int $id) {}
}
