<?php

namespace App\Application\Materias\Commands;

final readonly class CreateMateriaCommand
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
}
