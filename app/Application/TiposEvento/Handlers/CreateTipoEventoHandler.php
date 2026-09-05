<?php

namespace App\Application\TiposEvento\Handlers;

use App\Application\TiposEvento\Commands\CreateTipoEventoCommand;
use App\Application\TiposEvento\DTOs\TipoEventoDTO;
use App\Domain\TiposEvento\Contracts\TipoEventoRepositoryInterface;

class CreateTipoEventoHandler
{
    public function __construct(private readonly TipoEventoRepositoryInterface $repository) {}

    public function handle(CreateTipoEventoCommand $command): TipoEventoDTO
    {
        return $this->repository->create([
            'nombre' => $command->nombre,
            'color_hex' => $command->color_hex,
            'activo' => $command->activo,
        ]);
    }
}
