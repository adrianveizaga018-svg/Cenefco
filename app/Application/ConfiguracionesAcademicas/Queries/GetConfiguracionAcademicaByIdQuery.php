<?php

namespace App\Application\ConfiguracionesAcademicas\Queries;

final readonly class GetConfiguracionAcademicaByIdQuery
{
    public function __construct(public int $idConf) {}
}
