<?php

namespace App\Application\ConfiguracionesAcademicas\DTOs;

final readonly class ConfiguracionAcademicaDTO
{
    public function __construct(
        public int $idConf,
        public int $gestion,
        public int $idPlan,
        public ?string $descripcion,
        public int $estado,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            idConf: $row->id_conf,
            gestion: $row->gestion,
            idPlan: $row->id_plan,
            descripcion: $row->descripcion ?? null,
            estado: $row->estado,
        );
    }
}
