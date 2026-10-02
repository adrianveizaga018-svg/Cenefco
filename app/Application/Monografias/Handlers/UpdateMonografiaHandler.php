<?php

namespace App\Application\Monografias\Handlers;

use App\Application\Monografias\Commands\UpdateMonografiaCommand;
use App\Application\Monografias\DTOs\MonografiaDTO;
use App\Domain\Monografias\Contracts\MonografiaRepositoryInterface;

class UpdateMonografiaHandler
{
    public function __construct(private readonly MonografiaRepositoryInterface $repository) {}

    public function handle(UpdateMonografiaCommand $command): MonografiaDTO
    {
        $changes = array_filter([
            'titulo_monografia'      => $command->titulo_monografia,
            'descripcion_monografia' => $command->descripcion_monografia,
            'fecha_publicacion'      => $command->fecha_publicacion,
            'autor'                  => $command->autor,
            'archivo'                => $command->archivo,
            'estado'                 => $command->estado,
        ], fn ($v) => $v !== null);

        $this->repository->update($command->id, $changes);

        return MonografiaDTO::fromRow($this->repository->findById($command->id));
    }
}
