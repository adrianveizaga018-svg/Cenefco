<?php

namespace App\Application\Horarios\DTOs;

final readonly class HorarioDTO
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

    public static function fromModel(object $model): self
    {
        return new self(
            id_horar:    (int) $model->id_horar,
            id_us_reg:   isset($model->id_us_reg) ? (int) $model->id_us_reg : null,
            id_imp:      (int) $model->id_imp,
            id_d:        isset($model->id_d) ? (int) $model->id_d : null,
            hora_inicio: $model->hora_inicio ?? null,
            hora_fin:    $model->hora_fin ?? null,
            periodos:    $model->periodos ?? null,
            estado:      (int) $model->estado,
        );
    }
}
