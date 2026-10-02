<?php

namespace App\Application\Carreras\Handlers;

use App\Application\Carreras\Commands\UpdateCarreraCommand;
use App\Application\Carreras\DTOs\CarreraDTO;
use App\Domain\Carreras\Contracts\CarreraRepositoryInterface;

class UpdateCarreraHandler
{
    public function __construct(
        private readonly CarreraRepositoryInterface $repository
    ) {}

    public function handle(UpdateCarreraCommand $command): CarreraDTO
    {
        $data = array_filter([
            'nombre_carrera' => $command->nombre_carrera,
            'estado'         => $command->estado,
        ], fn ($v) => $v !== null);

        return $this->repository->update($command->id, $data);
    }
}
