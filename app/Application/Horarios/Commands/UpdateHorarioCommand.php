<?php

namespace App\Application\Horarios\Commands;

final readonly class UpdateHorarioCommand
{
    public function __construct(
        public int $id,
        public ?int $id_d,
        public ?string $hora_inicio,
        public ?string $hora_fin,
        public ?string $periodos,
        public ?int $estado,
    ) {}
}
