<?php

namespace App\Application\EnviosCertificado\Queries;

final readonly class GetEnviosCertificadoQuery
{
    public function __construct(public int $id_ins) {}
}
