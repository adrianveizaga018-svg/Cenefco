<?php

namespace App\Application\Materias\Commands;

final readonly class UpdateMateriaCommand
{
    public function __construct(
        public int $id,
        public ?string $sigla,
        public ?string $nombremat,
        public ?string $nombre,
        public ?string $semestre,
        public ?int $modalidad,
        public ?string $carga_horaria,
        public ?string $observacion,
        public ?int $estado,
    ) {}
}
