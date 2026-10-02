<?php

namespace App\Application\EfectosEspeciales\Handlers;

use App\Application\EfectosEspeciales\Commands\CreateEfectoEspecialCommand;
use App\Application\EfectosEspeciales\DTOs\EfectoEspecialDTO;
use App\Domain\EfectosEspeciales\Contracts\EfectoEspecialRepositoryInterface;

class CreateEfectoEspecialHandler
{
    public function __construct(private readonly EfectoEspecialRepositoryInterface $repository) {}

    public function handle(CreateEfectoEspecialCommand $command): EfectoEspecialDTO
    {
        return $this->repository->create([
            'nombre'           => $command->nombre,
            'tipo_efecto'      => $command->tipo_efecto,
            'color_primario'   => $command->color_primario,
            'color_secundario' => $command->color_secundario,
            'fecha_inicio'     => $command->fecha_inicio,
            'fecha_fin'        => $command->fecha_fin,
            'intensidad'       => $command->intensidad,
            'activo'           => $command->activo,
        ]);
    }
}
