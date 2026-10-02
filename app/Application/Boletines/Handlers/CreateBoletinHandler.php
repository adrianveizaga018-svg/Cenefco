<?php

namespace App\Application\Boletines\Handlers;

use App\Application\Boletines\Commands\CreateBoletinCommand;
use App\Application\Boletines\DTOs\BoletinDTO;
use App\Domain\Boletines\Contracts\BoletinRepositoryInterface;

class CreateBoletinHandler
{
    public function __construct(private readonly BoletinRepositoryInterface $repository) {}

    public function handle(CreateBoletinCommand $command): BoletinDTO
    {
        $row = $this->repository->create([
            'id_boletin'          => $command->id_boletin,
            'id_us_reg'           => $command->id_us_reg,
            'num_boletin'         => $command->num_boletin,
            'titulo_pagina'       => $command->titulo_pagina,
            'titulo_boletin'      => $command->titulo_boletin,
            'descripcion_boletin' => $command->descripcion_boletin,
            'estado'              => $command->estado,
            'imagen_url'          => $command->imagen_url,
            'fecha_reg'           => now(),
        ]);

        return BoletinDTO::fromRow($row);
    }
}
