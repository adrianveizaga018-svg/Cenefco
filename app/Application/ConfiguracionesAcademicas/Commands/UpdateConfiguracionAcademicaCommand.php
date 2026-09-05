<?php

namespace App\Application\ConfiguracionesAcademicas\Commands;

final readonly class UpdateConfiguracionAcademicaCommand
{
    public function __construct(
        public int $idConf,
        public ?int $gestion,
        public ?int $idPlan,
        public ?string $descripcion,
    ) {}
}
