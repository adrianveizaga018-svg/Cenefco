<?php

namespace App\Application\Honorarios\Commands;

final readonly class UpsertConfigHonorarioCommand
{
    public function __construct(
        public int     $id_programa,
        public string  $tipo_honorario,
        public ?float  $monto_fijo    = null,
        public ?float  $monto_por_dia = null,
    ) {}
}
