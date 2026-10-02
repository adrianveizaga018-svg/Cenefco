<?php

namespace App\Application\Boletines\Handlers;

use App\Application\Boletines\Commands\UpdateBoletinCommand;
use App\Application\Boletines\DTOs\BoletinDTO;
use App\Domain\Boletines\Contracts\BoletinRepositoryInterface;

class UpdateBoletinHandler
{
    public function __construct(private readonly BoletinRepositoryInterface $repository) {}

    public function handle(UpdateBoletinCommand $command): BoletinDTO
    {
        $changes = array_filter([
            'titulo_pagina'       => $command->titulo_pagina,
            'titulo_boletin'      => $command->titulo_boletin,
            'descripcion_boletin' => $command->descripcion_boletin,
            'estado'              => $command->estado,
            'imagen_url'          => $command->imagen_url,
        ], fn ($v) => $v !== null);

        $this->repository->update($command->id, $changes);

        return BoletinDTO::fromRow($this->repository->findById($command->id));
    }
}
