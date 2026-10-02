<?php

namespace App\Application\InscripcionesDiplomado\Commands;

final readonly class ConvertirInscripcionDiplomadoCommand
{
    public function __construct(
        public int  $id,
        public ?int $idImp  = null,
        public ?int $idPlan = null,
    ) {}
}
