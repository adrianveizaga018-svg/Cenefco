<?php

namespace App\Application\Horarios\Commands;

final readonly class CreateHorarioCommand
{
    public function __construct(
        public int $id_horar,
        public ?int $id_us_reg,
        public int $id_imp,
        public ?int $id_d,
        public ?string $hora_inicio,
        public ?string $hora_fin,
        public ?string $periodos,
        public int $estado,
    ) {}
}
