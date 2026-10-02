<?php

namespace App\Application\Trivia\Handlers;

use App\Application\Trivia\Commands\CreateTriviaCategoriaCommand;
use App\Application\Trivia\DTOs\TriviaCategoriaDTO;
use App\Domain\Trivia\Contracts\TriviaCategoriaRepositoryInterface;

class CreateTriviaCategoriaHandler
{
    public function __construct(
        private readonly TriviaCategoriaRepositoryInterface $repository
    ) {}

    public function handle(CreateTriviaCategoriaCommand $command): TriviaCategoriaDTO
    {
        return $this->repository->create([
            'nombre' => $command->nombre,
            'descripcion' => $command->descripcion,
            'imagen_url' => $command->imagen_url,
            'color' => $command->color,
            'curso_id' => $command->curso_id,
            'orden' => $command->orden,
            'activo' => $command->activo,
        ]);
    }
}
