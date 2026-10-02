<?php

namespace App\Application\Certificados\Queries;

final readonly class GetCertificadoByIdQuery
{
    public function __construct(public int $id) {}
}
