<?php

namespace App\Application\Carreras\DTOs;

final readonly class CarreraDTO
{
    public function __construct(
        public int $id_carrera,
        public ?int $id_us_reg,
        public ?int $num_carrera,
        public string $nombre_carrera,
        public int $estado,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id_carrera:    (int) $model->id_carrera,
            id_us_reg:     isset($model->id_us_reg) ? (int) $model->id_us_reg : null,
            num_carrera:   isset($model->num_carrera) ? (int) $model->num_carrera : null,
            nombre_carrera: $model->nombre_carrera,
            estado:        (int) $model->estado,
        );
    }
}
