<?php

namespace App\Application\ConfiguracionesAcademicas\Commands;

final readonly class DeleteConfiguracionAcademicaCommand
{
    public function __construct(public int $idConf) {}
}
