<?php

namespace App\Application\Notas\Handlers;

use App\Application\Notas\Commands\CreateNotaCommand;
use App\Application\Notas\DTOs\NotaDTO;
use App\Domain\Notas\Contracts\NotaRepositoryInterface;

class CreateNotaHandler
{
    public function __construct(
        private readonly NotaRepositoryInterface $repository
    ) {}

    public function handle(CreateNotaCommand $command): NotaDTO
    {
        return $this->repository->create([
            'id_not'           => $command->id_not,
            'id_us_reg'        => $command->id_us_reg ?? 0,
            'periodo'          => $command->periodo,
            'gestion'          => $command->gestion,
            'id_imp'           => $command->id_imp,
            'id_us'            => $command->id_us,
            'id_mat'           => $command->id_mat,
            'nota'             => $command->nota,
            'nota_seg'         => $command->nota_seg,
            'paralelo'         => $command->paralelo,
            'mostrarcert_notas' => 1,
            'estado'           => $command->estado,
            'fecha_reg'        => now(),
        ]);
    }
}
