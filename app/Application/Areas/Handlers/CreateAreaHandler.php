<?php

namespace App\Application\Areas\Handlers;

use App\Application\Areas\Commands\CreateAreaCommand;
use App\Application\Areas\DTOs\AreaDTO;
use App\Domain\Areas\Contracts\AreaRepositoryInterface;

class CreateAreaHandler
{
    public function __construct(
        private readonly AreaRepositoryInterface $repository,
    ) {}

    public function handle(CreateAreaCommand $command): AreaDTO
    {
        return $this->repository->create([
            'titulo'           => $command->titulo,
            'slug'             => $command->slug,
            'descripcion'      => $command->descripcion,
            'logo_url'         => $command->logo_url,
            'logo_alt'         => $command->logo_alt,
            'galeria'          => $command->galeria,
            'color'            => $command->color,
            'icono'            => $command->icono,
            'orden'            => $command->orden,
            'activo'           => $command->activo,
            'meta_titulo'      => $command->meta_titulo,
            'meta_descripcion' => $command->meta_descripcion,
        ]);
    }
}
