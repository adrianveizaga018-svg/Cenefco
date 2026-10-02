<?php

namespace App\Application\Certificados\Queries;

final readonly class GetCertificadoByCodigoQuery
{
    public function __construct(public string $codigo) {}
}
