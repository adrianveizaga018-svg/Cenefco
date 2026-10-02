<?php

namespace App\Application\Tesis\Handlers;

use App\Application\Tesis\Commands\UpdateTesisCommand;
use App\Application\Tesis\DTOs\TesisDTO;
use App\Domain\Tesis\Contracts\TesisRepositoryInterface;

class UpdateTesisHandler
{
    public function __construct(private readonly TesisRepositoryInterface $repository) {}

    public function handle(UpdateTesisCommand $command): TesisDTO
    {
        $changes = array_filter([
            'titulo_tesis'      => $command->titulo_tesis,
            'descripcion_tesis' => $command->descripcion_tesis,
            'fecha_publicacion' => $command->fecha_publicacion,
            'autor'             => $command->autor,
            'tipo_tesis'        => $command->tipo_tesis,
            'archivo'           => $command->archivo,
            'estado'            => $command->estado,
        ], fn ($v) => $v !== null);

        $this->repository->update($command->id, $changes);

        return TesisDTO::fromRow($this->repository->findById($command->id));
    }
}
