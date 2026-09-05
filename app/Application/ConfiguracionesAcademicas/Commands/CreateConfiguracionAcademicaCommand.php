<?php

namespace App\Application\ConfiguracionesAcademicas\Commands;

final readonly class CreateConfiguracionAcademicaCommand
{
    public function __construct(
        public int $idConf,
        public int $gestion,
        public int $idPlan,
        public ?string $descripcion,
        public int $idUsReg,
    ) {}
}
