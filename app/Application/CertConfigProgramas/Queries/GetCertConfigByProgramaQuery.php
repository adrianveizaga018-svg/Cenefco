<?php

namespace App\Application\CertConfigProgramas\Queries;

final readonly class GetCertConfigByProgramaQuery
{
    public function __construct(public int $programa_id) {}
}
