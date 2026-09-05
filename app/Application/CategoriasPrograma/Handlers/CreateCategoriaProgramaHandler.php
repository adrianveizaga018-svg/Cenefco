<?php

namespace App\Application\CategoriasPrograma\Handlers;

use App\Application\CategoriasPrograma\Commands\CreateCategoriaProgramaCommand;
use App\Application\CategoriasPrograma\DTOs\CategoriaProgramaDTO;
use App\Domain\CategoriasPrograma\Contracts\CategoriaProgramaRepositoryInterface;

class CreateCategoriaProgramaHandler
{
    public function __construct(
        private readonly CategoriaProgramaRepositoryInterface $repository,
    ) {}

    public function handle(CreateCategoriaProgramaCommand $command): CategoriaProgramaDTO
    {
        return $this->repository->create([
            'nombre'           => $command->nombre,
            'slug'             => $command->slug,
            'descripcion'      => $command->descripcion,
            'imagen_url'       => $command->imagen_url,
            'imagen_alt'       => $command->imagen_alt,
            'icono'            => $command->icono,
            'color'            => $command->color,
            'orden'            => $command->orden,
            'activo'           => $command->activo,
            'meta_titulo'      => $command->meta_titulo,
            'meta_descripcion' => $command->meta_descripcion,
            'tipo_programa_id' => $command->tipo_programa_id,
            'comision_monto'   => $command->comision_monto,
        ]);
    }
}
