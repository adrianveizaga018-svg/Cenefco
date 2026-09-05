<?php

namespace App\Application\Tesis\Handlers;

use App\Application\Tesis\Commands\CreateTesisCommand;
use App\Application\Tesis\DTOs\TesisDTO;
use App\Domain\Tesis\Contracts\TesisRepositoryInterface;

class CreateTesisHandler
{
    public function __construct(private readonly TesisRepositoryInterface $repository) {}

    public function handle(CreateTesisCommand $command): TesisDTO
    {
        $row = $this->repository->create([
            'id_tesis'          => $command->id_tesis,
            'id_us_reg'         => $command->id_us_reg,
            'num_tesis'         => $command->num_tesis,
            'titulo_tesis'      => $command->titulo_tesis,
            'descripcion_tesis' => $command->descripcion_tesis,
            'fecha_publicacion' => $command->fecha_publicacion,
            'autor'             => $command->autor,
            'tipo_tesis'        => $command->tipo_tesis,
            'archivo'           => $command->archivo,
            'estado'            => $command->estado,
            'fecha_reg'         => now(),
        ]);

        return TesisDTO::fromRow($row);
    }
}
