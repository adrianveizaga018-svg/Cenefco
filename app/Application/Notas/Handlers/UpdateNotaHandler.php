<?php

namespace App\Application\Notas\Handlers;

use App\Application\Notas\Commands\UpdateNotaCommand;
use App\Application\Notas\DTOs\NotaDTO;
use App\Domain\Notas\Contracts\NotaRepositoryInterface;

class UpdateNotaHandler
{
    public function __construct(
        private readonly NotaRepositoryInterface $repository
    ) {}

    public function handle(UpdateNotaCommand $command): NotaDTO
    {
        $data = array_filter([
            'nota'             => $command->nota,
            'nota_seg'         => $command->nota_seg,
            'paralelo'         => $command->paralelo,
            'mostrarcert_notas' => $command->mostrarcert_notas,
            'estado'           => $command->estado,
        ], fn ($v) => $v !== null);

        return $this->repository->update($command->id, $data);
    }
}
