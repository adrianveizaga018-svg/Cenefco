<?php

namespace App\Application\CertVerificaciones\Queries;

final readonly class GetCertVerificacionByIdQuery
{
    public function __construct(public int $id) {}
}
