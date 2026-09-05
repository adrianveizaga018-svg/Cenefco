<?php

namespace App\Application\Revistas\Handlers;

use App\Application\Revistas\Commands\UpdateRevistaCommand;
use App\Application\Revistas\DTOs\RevistaDTO;
use App\Domain\Revistas\Contracts\RevistaRepositoryInterface;

class UpdateRevistaHandler
{
    public function __construct(private readonly RevistaRepositoryInterface $repository) {}

    public function handle(UpdateRevistaCommand $command): RevistaDTO
    {
        $changes = array_filter([
            'titulo_revista'      => $command->titulo_revista,
            'descripcion_revista' => $command->descripcion_revista,
            'fecha_publicacion'   => $command->fecha_publicacion,
            'archivo'             => $command->archivo,
            'estado'              => $command->estado,
        ], fn ($v) => $v !== null);

        $this->repository->update($command->id, $changes);

        return RevistaDTO::fromRow($this->repository->findById($command->id));
    }
}
