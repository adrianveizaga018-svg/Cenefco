<?php

namespace App\Application\Revistas\Handlers;

use App\Application\Revistas\Commands\CreateRevistaCommand;
use App\Application\Revistas\DTOs\RevistaDTO;
use App\Domain\Revistas\Contracts\RevistaRepositoryInterface;

class CreateRevistaHandler
{
    public function __construct(private readonly RevistaRepositoryInterface $repository) {}

    public function handle(CreateRevistaCommand $command): RevistaDTO
    {
        $row = $this->repository->create([
            'id_revista'          => $command->id_revista,
            'id_us_reg'           => $command->id_us_reg,
            'num_revista'         => $command->num_revista,
            'titulo_revista'      => $command->titulo_revista,
            'descripcion_revista' => $command->descripcion_revista,
            'fecha_publicacion'   => $command->fecha_publicacion,
            'archivo'             => $command->archivo,
            'estado'              => $command->estado,
            'fecha_reg'           => now(),
        ]);

        return RevistaDTO::fromRow($row);
    }
}
