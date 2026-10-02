<?php

namespace App\Application\Materias\Handlers;

use App\Application\Materias\Commands\UpdateMateriaCommand;
use App\Application\Materias\DTOs\MateriaDTO;
use App\Domain\Materias\Contracts\MateriaRepositoryInterface;

class UpdateMateriaHandler
{
    public function __construct(
        private readonly MateriaRepositoryInterface $repository
    ) {}

    public function handle(UpdateMateriaCommand $command): MateriaDTO
    {
        $data = array_filter([
            'sigla'         => $command->sigla,
            'nombremat'     => $command->nombremat,
            'nombre'        => $command->nombre,
            'semestre'      => $command->semestre,
            'modalidad'     => $command->modalidad,
            'carga_horaria' => $command->carga_horaria,
            'observacion'   => $command->observacion,
            'estado'        => $command->estado,
        ], fn ($v) => $v !== null);

        return $this->repository->update($command->id, $data);
    }
}
