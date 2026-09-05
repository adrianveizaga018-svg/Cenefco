<?php

namespace App\Application\Fotos\Handlers;

use App\Application\Fotos\Commands\UpdateFotoCommand;
use App\Application\Fotos\DTOs\FotoDTO;
use App\Domain\Fotos\Contracts\FotoRepositoryInterface;

class UpdateFotoHandler
{
    public function __construct(private readonly FotoRepositoryInterface $repository) {}

    public function handle(UpdateFotoCommand $command): FotoDTO
    {
        $changes = array_filter([
            'titulo_foto'      => $command->titulo_foto,
            'descripcion_foto' => $command->descripcion_foto,
            'foto'             => $command->foto,
            'fecha_foto'       => $command->fecha_foto,
            'estado'           => $command->estado,
        ], fn ($v) => $v !== null);

        $this->repository->update($command->id, $changes);

        return FotoDTO::fromRow($this->repository->findById($command->id));
    }
}
