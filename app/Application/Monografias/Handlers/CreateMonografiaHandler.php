<?php

namespace App\Application\Monografias\Handlers;

use App\Application\Monografias\Commands\CreateMonografiaCommand;
use App\Application\Monografias\DTOs\MonografiaDTO;
use App\Domain\Monografias\Contracts\MonografiaRepositoryInterface;

class CreateMonografiaHandler
{
    public function __construct(private readonly MonografiaRepositoryInterface $repository) {}

    public function handle(CreateMonografiaCommand $command): MonografiaDTO
    {
        $row = $this->repository->create([
            'id_monografia'          => $command->id_monografia,
            'id_us_reg'              => $command->id_us_reg,
            'num_monografia'         => $command->num_monografia,
            'titulo_monografia'      => $command->titulo_monografia,
            'descripcion_monografia' => $command->descripcion_monografia,
            'fecha_publicacion'      => $command->fecha_publicacion,
            'autor'                  => $command->autor,
            'archivo'                => $command->archivo,
            'estado'                 => $command->estado,
            'fecha_reg'              => now(),
        ]);

        return MonografiaDTO::fromRow($row);
    }
}
