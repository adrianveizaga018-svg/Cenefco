<?php

namespace App\Application\Materias\DTOs;

final readonly class MateriaDTO
{
    public function __construct(
        public int $id_mat,
        public ?int $id_us_reg,
        public ?string $sigla,
        public ?string $nombremat,
        public string $nombre,
        public ?string $semestre,
        public ?int $modalidad,
        public ?string $carga_horaria,
        public ?string $observacion,
        public int $estado,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id_mat:        (int) $model->id_mat,
            id_us_reg:     isset($model->id_us_reg) ? (int) $model->id_us_reg : null,
            sigla:         $model->sigla ?? null,
            nombremat:     $model->nombremat ?? null,
            nombre:        $model->nombre,
            semestre:      $model->semestre ?? null,
            modalidad:     isset($model->modalidad) ? (int) $model->modalidad : null,
            carga_horaria: $model->carga_horaria ?? null,
            observacion:   $model->observacion ?? null,
            estado:        (int) $model->estado,
        );
    }
}
