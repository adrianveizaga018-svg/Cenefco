<?php

namespace App\Application\RevistasCientificas\Handlers;

use App\Application\RevistasCientificas\Commands\CreateRevistaCientificaCommand;
use App\Application\RevistasCientificas\DTOs\RevistaCientificaDTO;
use App\Domain\RevistasCientificas\Contracts\RevistaCientificaRepositoryInterface;

class CreateRevistaCientificaHandler
{
    public function __construct(private readonly RevistaCientificaRepositoryInterface $repository) {}

    public function handle(CreateRevistaCientificaCommand $command): RevistaCientificaDTO
    {
        $row = $this->repository->create([
            'id_revistacientifica'          => $command->id_revistacientifica,
            'id_us_reg'                     => $command->id_us_reg,
            'num_revistacientifica'         => $command->num_revistacientifica,
            'titulo_revistacientifica'      => $command->titulo_revistacientifica,
            'descripcion_revistacientifica' => $command->descripcion_revistacientifica,
            'fecha_publicacion'             => $command->fecha_publicacion,
            'archivo'                       => $command->archivo,
            'estado'                        => $command->estado,
            'fecha_reg'                     => now(),
        ]);

        return RevistaCientificaDTO::fromRow($row);
    }
}
