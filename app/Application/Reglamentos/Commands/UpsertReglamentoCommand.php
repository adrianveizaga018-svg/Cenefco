<?php

namespace App\Application\Reglamentos\Commands;

final readonly class UpsertReglamentoCommand
{
    public function __construct(
        public int     $idPrograma,
        public ?string $bienvenida,
        public ?string $reglasAsistencia,
        public ?string $reglasEvaluacion,
        public ?string $reglasPagos,
        public ?string $reglasConducra,
        public ?string $reglasPlataformas,
        public ?string $reglasDerechos,
    ) {}
}
