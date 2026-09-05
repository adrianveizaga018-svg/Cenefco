<?php

namespace App\Application\Materias\Handlers;

use App\Application\Materias\Commands\CreateMateriaCommand;
use App\Application\Materias\DTOs\MateriaDTO;
use App\Domain\Materias\Contracts\MateriaRepositoryInterface;

class CreateMateriaHandler
{
    public function __construct(
        private readonly MateriaRepositoryInterface $repository
    ) {}

    public function handle(CreateMateriaCommand $command): MateriaDTO
    {
        return $this->repository->create([
            'id_mat'        => $command->id_mat,
            'id_us_reg'     => $command->id_us_reg ?? 0,
            'sigla'         => $command->sigla,
            'nombremat'     => $command->nombremat,
            'nombre'        => $command->nombre,
            'semestre'      => $command->semestre,
            'modalidad'     => $command->modalidad,
            'carga_horaria' => $command->carga_horaria,
            'observacion'   => $command->observacion,
            'estado'        => $command->estado,
            'fecha_reg'     => now(),
        ]);
    }
}
