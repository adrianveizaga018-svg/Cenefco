<?php

namespace App\Application\Fotos\Handlers;

use App\Application\Fotos\Commands\CreateFotoCommand;
use App\Application\Fotos\DTOs\FotoDTO;
use App\Domain\Fotos\Contracts\FotoRepositoryInterface;

class CreateFotoHandler
{
    public function __construct(private readonly FotoRepositoryInterface $repository) {}

    public function handle(CreateFotoCommand $command): FotoDTO
    {
        $row = $this->repository->create([
            'id_foto'          => $command->id_foto,
            'id_us_reg'        => $command->id_us_reg,
            'num_foto'         => $command->num_foto,
            'titulo_foto'      => $command->titulo_foto,
            'descripcion_foto' => $command->descripcion_foto,
            'foto'             => $command->foto,
            'fecha_foto'       => $command->fecha_foto,
            'estado'           => $command->estado,
            'fecha_reg'        => now(),
        ]);

        return FotoDTO::fromRow($row);
    }
}
